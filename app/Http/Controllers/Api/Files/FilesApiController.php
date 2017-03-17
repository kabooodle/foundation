<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Files;

use Binput;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Api\AbstractApiController;

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
        return $this->createPresignedDateV4($request);

        $this->validate($request, $this->rules());

        $user = $this->getUser();
        $filename = Binput::get('filename', false);

        if (!$filename || !$user) {
            return $this->setStatusCode(500)->respond();
        }

        // Only allow alphanumeric - _ . in filenames.  Anything else, replace with _
        $filename = preg_replace('/[^A-Za-z0-9.-_]/i', '_', strtolower($filename));
        $filename = str_replace('jpeg', 'jpg', $filename);
        $filename = str_random(32) . $filename;

        $acl = 'public-read';
        $bucket = env('AWS_BUCKET');
        $awsSecretKey = env('AWS_SECRET');
        $awsSecretKeyId = env('AWS_KEY');

        // Add 48 hours from now() to generate the expiration timestamp.
        $expiresOn = date('Y-m-d\TG:i:s\Z', strtotime('+ 48 hours', strtotime(date("c"))));

        // Preface the filename with the object path which for now is the user id.
        $filePath = 'resources/'.$user->id.'/'.$filename;

        $policyDocument = '
		{"expiration": "'.$expiresOn.'",
		  "conditions": [
		    {"bucket": "'.$bucket.'"},
		    ["starts-with", "$key", "'.$filePath.'"],
		    ["content-length-range",0,5242880],
		    {"acl": "'.$acl.'"},
		    {"success_action_status": "201"},
		  ]
		}';

        $policy = base64_encode($policyDocument);
        $signature = hex2b64(hmacsha1($awsSecretKey, $policy));

        $this->setData([
            'filename' => $filename,
            'url' => "https://{$bucket}.s3.amazonaws.com",
            'key' => $filePath,
            'path' => $filePath,
            'acl' => $acl,
            'AWSAccessKeyId' => $awsSecretKeyId,
            'policy' => $policy,
            'signature' => $signature,
        ]);

        return $this->respond();
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'filename' => 'required'
        ];
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function createPresignedDateV4(Request $request)
    {
        $this->validate($request, $this->rules());

        $user = $this->getUser();
        $filename = Binput::get('filename', false);

        if (!$filename || !$user) {
            return $this->setStatusCode(500)->respond();
        }

        // Only allow alphanumeric - _ . in filenames.  Anything else, replace with _
        $filename = preg_replace('/[^A-Za-z0-9.-_]/i', '_', strtolower($filename));
        $filename = str_replace('jpeg', 'jpg', $filename);
        $filename = str_random(32) . $filename;

        $region = 'us-west-1';
        $s3Bucket = env('AWS_BUCKET');
        $awsKey = env('AWS_KEY');
        $awsSecret = env('AWS_SECRET');

        $acl = 'public-read';
        $algorithm = "AWS4-HMAC-SHA256";
        $service = "s3";
        $date = gmdate("Ymd\THis\Z");
        $shortDate = gmdate("Ymd");
        $requestType = "aws4_request";
        $filePath = 'resources/'.$user->id.'/'.$filename;

        $scope = [
            $awsKey,
            $shortDate,
            $region,
            $service,
            $requestType
        ];
        $credentials = implode('/', $scope);

        $expiresOn = date('Y-m-d\TG:i:s\Z', strtotime('+ 48 hours', strtotime(date("c"))));

        $policy = '
		{"expiration": "'.$expiresOn.'",
		  "conditions": [
		    {"bucket": "'.$s3Bucket.'"},
		    ["starts-with", "$key", ""],
		    {"acl": "'.$acl.'"},
		    {"success_action_status": "201"},
		    {"x-amz-date" : "'.$date.'"},		    
		    {"x-amz-algorithm": "AWS4-HMAC-SHA256"},		    
		    {"x-amz-credential": "'.$credentials.'"},
            {"x-amz-algorithm": "AWS4-HMAC-SHA256"},
		  ]
		}';

        $base64Policy = base64_encode(($policy));

        $dateKey = hash_hmac('sha256', $shortDate, 'AWS4' . $awsSecret, true);
        $dateRegionKey = hash_hmac('sha256', $region, $dateKey, true);
        $dateRegionServiceKey = hash_hmac('sha256', $service, $dateRegionKey, true);
        $signingKey = hash_hmac('sha256', $requestType, $dateRegionServiceKey, true);

        $signature = hash_hmac('sha256', $base64Policy, $signingKey);
//        $url = "//{$s3Bucket}.{$service}-{$region}.amazonaws.com";
        $url = "https://{$s3Bucket}.s3.amazonaws.com";
        $this->setData([
            'credential' => $credentials,
            'algorithm' => $algorithm,
            'date' => $date,
            'acl' => 'public-read',
            'url' => $url,
            'key' => $filePath,
            'path' => $filePath,
            'policy' => $base64Policy,
            'signature' => $signature,
        ]);

        return $this->respond();
    }
}
