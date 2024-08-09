<?php

class AMOHelper {
	public function getToken() {
	    $accessToken = json_decode(file_get_contents(TOKEN_FILE), true);

	    if (
	        isset($accessToken)
	        && isset($accessToken['accessToken'])
	        && isset($accessToken['refreshToken'])
	        && isset($accessToken['expires'])
	        && isset($accessToken['baseDomain'])
	    ) {
	        return new \League\OAuth2\Client\Token\AccessToken([
	            'access_token' => $accessToken['accessToken'],
	            'refresh_token' => $accessToken['refreshToken'],
	            'expires' => $accessToken['expires'],
	            'baseDomain' => $accessToken['baseDomain'],
	        ]);
	    } else {
	        exit('Invalid access token ' . var_export($accessToken, true));
	    }
	}

	public function saveToken($accessToken) {
	    if (
	        isset($accessToken)
	        && isset($accessToken['accessToken'])
	        && isset($accessToken['refreshToken'])
	        && isset($accessToken['expires'])
	        && isset($accessToken['baseDomain'])
	    ) {
	        $data = [
	            'accessToken' => $accessToken['accessToken'],
	            'expires' => $accessToken['expires'],
	            'refreshToken' => $accessToken['refreshToken'],
	            'baseDomain' => $accessToken['baseDomain'],
	        ];

	        file_put_contents(TOKEN_FILE, json_encode($data));
	    } else {
	        exit('Invalid access token ' . var_export($accessToken, true));
	    }
	}

}