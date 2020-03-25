<?php 

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class ShortenRepository {

    public function GetURLFromCode($code) {
        return DB::table('shorten')->where('shortcode', '=', $code)->first();
    }

    public function StoreShortcode($data) {
        return DB::table('shorten')->insert($data);
    }

}