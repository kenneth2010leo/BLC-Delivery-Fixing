<?php

namespace App\Http\Controllers;

use App\Events\UpdateRevenue;
use App\Models\Items;
use App\Models\Player;
use App\Models\PurchaseRawItemHistory;
use App\Models\Room;
use App\Models\RawItem;
use App\Models\RevenueHistory;
use Illuminate\Http\Request;
use Termwind\Components\Raw;

class SettingBahanBaku extends Controller
{
    public function index($room_id)
    {
        $room = Room::where('room_id', $room_id)->first();
        $itemChosen = json_decode($room->item_chosen, true);

        $rawItem = [];

        foreach ($itemChosen as $item) {
            $i = Items::where('id', $item)->first();
            if ($i) {
                $rawItemsNeeded = is_string($i->raw_item_needed) ? json_decode($i->raw_item_needed, true) : $i->raw_item_needed;

                if (is_array($rawItemsNeeded)) {
                    foreach ($rawItemsNeeded as $raw) {
                        $rawItem[] = $raw;
                    }
                }
            }
        }
        $rawItem = array_unique($rawItem);
        $rawItemChosen = [];
        foreach ($rawItem as $raw) {
            $rawItemChosen[] = RawItem::where('id', $raw)->first();
        }
        $purchaseHistory = PurchaseRawItemHistory::where('room_id', $room_id)->get();

        // Prepare purchase history without consolidation
        $purchaseHistoryData = [];
        foreach ($purchaseHistory as $history) {
            $rawItems = json_decode($history->raw_items);
            $items = [];
            foreach ($rawItems as $raw) {
                if ($raw->quantity > 0) {
                    $itemId = $raw->item_id;
                    $items[] = [
                        'item_name' => RawItem::find($itemId)->raw_item_name,
                        'quantity' => $raw->quantity,
                    ];
                }
            }
            $purchaseHistoryData[] = [
                'player_username' => $history->player_username,
                'day' => $history->day,
                'items' => $items,
                'total_cost' => $history->total_price,
                'revenue_before' => $history->revenue_before,
                'revenue_after' => $history->revenue_after,
            ];
        }

        return view('Admin.fitur.bahan_baku', [
            'room' => $room,
            'players' => Player::where('room_id', $room_id)->get(),
            'rawItems' => $rawItemChosen,
            'purchaseHistory' => $purchaseHistoryData
        ]);
    }


    public function setting(Request $request)
    {
        //validasi struktur dan nilai quantity
        $request->validate([
            'items'            => 'required|array|min:1',
            'items.*.item_id'  => 'required|integer|distinct|exists:raw_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'items.required'            => 'Minimal pilih 1 bahan baku',
            'items.min'                 => 'Minimal pilih 1 bahan baku',
            'items.*.quantity.min'      => 'Kuantitas minimal 1 per bahan baku',
            'items.*.quantity.required' => 'Kuantitas wajib diisi',
        ]);

        //total barang harus minimal 1
        $totalQuantity = collect($request->input('items'))->sum('quantity');
        if ($totalQuantity < 1) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Minimal pembelian bahan baku adalah 1'
            ]);
        }

        $price = 0;
        for ($i = 0; $i < count($request->input('items')); $i++) {
            $query = RawItem::where('id', $request->input('items')[$i]['item_id'])->first();
            $price = $price + ($query->raw_item_price * $request->input('items')[$i]['quantity']);
        }

        $query = Player::where('player_username', $request->player_id)->first();
        $room = Room::where('room_id', $request->room_id)->first();
        if ($query->purchased == 0) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Player can only purchased once !!!'
            ]);
        }
        if ($query->revenue < $price) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Not enough cash !!!'
            ]);
        } else {

            //update Inventory Raw Items
            //quantity berdasarkan id
            $purchased = [];
            foreach ($request->input('items') as $item) {
                $id = (int) $item['item_id'];
                $purchased[$id] = ($purchased[$id] ?? 0) + (int) $item['quantity'];
            }

            //urutan id bahan baku di inventory pemain
            $rawIdOrder = [];
            foreach (json_decode($room->item_chosen, true) as $itemId) {
                $i = Items::where('id', $itemId)->first();
                if ($i) {
                    $needed = is_string($i->raw_item_needed)
                        ? json_decode($i->raw_item_needed, true)
                        : $i->raw_item_needed;

                    if (is_array($needed)) {
                        foreach ($needed as $raw) {
                            $rawIdOrder[] = (int) $raw;
                        }
                    }
                }
            }
            $rawIdOrder = array_values(array_unique($rawIdOrder));

            //tambah quantity sesuai id, bukan sesuai urutan request
            $currentRawItems = json_decode($query->raw_items);
            foreach ($rawIdOrder as $pos => $rawId) {
                $currentRawItems[$pos] = ($currentRawItems[$pos] ?? 0) + ($purchased[$rawId] ?? 0);
            }

            $query->raw_items = json_encode($currentRawItems);

            // Potong saldo
            $prevRenue = $query->revenue;
            $query->revenue = $query->revenue - $price;
            $query->purchased = 0;
            $query->save();

            $history = new PurchaseRawItemHistory();
            $history->room_id = $request->input('room_id');
            $history->day = $room->recent_day;
            $history->player_username = $request->input('player_id');
            $history->raw_items = json_encode($request->input('items'));
            $history->total_price = $price;
            $history->revenue_before = $prevRenue;
            $history->revenue_after = $query->revenue;
            $history->save();

            $revenueHistory = new RevenueHistory();
            $revenueHistory->room_id = $request->room_id;
            $revenueHistory->day = $room->recent_day;
            $revenueHistory->player_username = $request->player_id;
            $revenueHistory->transaction_description = 'Raw Item Transaction';
            $revenueHistory->revenue_before = $prevRenue;
            $revenueHistory->revenue_after = $query->revenue;
            $revenueHistory->value = $price * -1;
            $revenueHistory->save();

            UpdateRevenue::dispatch($request->player_id, $request->room_id);

            return response()->json([
                'status' => 'success',
                'message' => 'Pembelian Berhasil',
                'price' => $price
            ]);
        }
    }
}
