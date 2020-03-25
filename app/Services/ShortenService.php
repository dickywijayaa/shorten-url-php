<?php

namespace App\Services;

use App\Repositories\ShortenRepository;

class ShortenService {

    public function __construct()
    {
        $this->repository = new ShortenRepository;
        $this->response = [
            'message' => 'process failed',
            'data' => [],
            'status' => false
        ];
    }

    public function FetchURLByCode($code) {
        $result = $this->repository->GetURLFromCode($code);
        if ($result) {
            $this->response['message'] = 'success';
            $this->response['status'] = true;
            $this->response['data'] = $result;
        }
        return $this->response;
    }

    public function StoreShortenURL($data) {
        // check if code exists
        if (!empty($data['shortcode'])) {
            $check = $this->repository->GetURLFromCode($data['shortcode']);
            if ($check) {
                $this->response['message'] = 'shortcode already exists';
                return $this->response;
            }
        } else {
            $pass = false;
            do {
                $data['shortcode'] = str_random(6);
                $check = $this->repository->GetURLFromCode($data['shortcode']);
                $pass = ($check) ? false : true; // true if code didn't exist in database
            } while (!$pass);
        }

        $insert = [
            'url' => $data['url'],
            'shortcode' => $data['shortcode'],
            'created_at' => date('Y-m-d H:i:s')
        ];

        $result = $this->repository->StoreShortcode($insert);
        if ($result) {
            $this->response['message'] = 'successfully insert url';
            $this->response['data'] = $data;
            $this->response['status'] = true;
            return $this->response;
        }

        $this->response['message'] = 'failed insert url';
        return $this->response;
    }

}