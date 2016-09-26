<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api;

use Binput;
use Kabooodle\Models\User;
use Illuminate\Http\Request;

/**
 * Class FilesApiController
 * @package Kabooodle\Http\Controllers\Api
 */
class FilesApiController extends AbstractApiController
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function createPresignedData(Request $request)
    {
        $this->validate($request, $this->rules());

        $user = User::where('public_hash', Binput::get('user'))->first();
        $filename = Binput::get('filename', false);

        $acl = 'public-read';
        $bucket = env('AWS_BUCKET');
        $awsSecretKey = env('AWS_SECRET');

        // Add 48 hours from now() to generate the expiration timestamp.
        $expiresOn = date('Y-m-d\TG:i:s\Z', strtotime('+ 48 hours', strtotime(date("c"))));

        $policyDocument = '
		{"expiration": "'.$expiresOn.'",
		  "conditions": [
		    {"bucket": "'.$bucket.'"},
		    ["starts-with", "$key", "'.$filename.'"],
		    {"acl": "'.$acl.'"},
		    {"success_action_status": "201"},
		  ]
		}';

        $policy = base64_encode($policyDocument);
        $signature = hex2b64(hmacsha1($awsSecretKey, $policy));

        $data = [
            'policy' => $policy,
            'signature' => $signature,
            'document' => $policyDocument,
            'expires' => $expiresOn
        ];

        $this->setData([
            'key' => $filename,
            'path' => $filename,
            'acl' => 'public-read',
            'AWSAccessKeyId' => env('AWS_KEY'),
            'policy' => $data['policy'],
            'signature' => $data['signature'],
        ]);

        return $this->respond();
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'user' => 'required|exists:users,public_hash',
            'filename' => 'required'
        ];
    }
}