<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Files;

use Binput;
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
        $this->validate($request, $this->rules());

        $user = $this->getUser();
        $filename = Binput::get('filename', false);

        if (!$filename || !$user) {
            return $this->setStatusCode(500)->respond();
        }

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
		    {"acl": "'.$acl.'"},
		    {"success_action_status": "201"},
		  ]
		}';

        $policy = base64_encode($policyDocument);
        $signature = hex2b64(hmacsha1($awsSecretKey, $policy));

        $this->setData([
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
     * WIP FOR V4 SIGNING REQUESTS
     * Currently porting Java version (bottom) to php
     *
     * @param Request $request
     */
    public function createPresignedDateV4(Request $request)
    {
        $this->validate($request, $this->rules());

        $filename = Binput::get('filename', false);

        $bucket = env('AWS_BUCKET');
        $awsSecretId = env('AWS_KEY');

        // Timestamp user for signing
        $signingDate = date('Y-m-d');

        $signingDate = date('Y-m-d');

        // Add 48 hours from now() to generate the expiration timestamp.
        $expiresOn = date('Y-m-d\TG:i:s\Z', strtotime('+ 48 hours', strtotime(date("c"))));

        $awsRegion = 'us-west-1';

        $signingCredential = awsUser.getAwsAccessKeyId() + "/" + date('Ymd', strtotime($signingDate)) + "/" + awsRegion + "/s3/aws4_request";

        $policyDocument4 = '
		{"expiration": "'.$expiresOn.'",
		  "conditions": [
		    {"bucket": "'.$bucket.'"},
		    ["starts-with", "$key", "'.$filename.'"],
		    {"acl": "public-read"},
		    {"x-amz-algorithm": "AWS4-HMAC-SHA256"},
		    {"x-amz-date" : "'.$signingDate.'T000000Z"},
		    {"success_action_status": "201"},
		  ]
		}';

        $this->setData([
            'x-amx-algorithm' =>'',
            'x-amz-credential' => '',
            'x-amz-date' =>'',
            'x-amx-signature' => '',
            'key' => $filename,
            'path' => $filename,
            'acl' => 'public-read',
            'AWSAccessKeyId' => env('AWS_KEY'),
            'policy' => $data['policy'],
            'signature' => $data['signature'],
        ]);
    }

    /**
     * @param string $key
     * @param string $dateStamp
     * @param string $regionName
     * @param string $serviceName
     *
     * @return string
     */
    public function getSignatureKey(string $key, string $dateStamp, string $regionName, string $serviceName = 's3')
    {
        $kSecret = 'AWS4' + $key;
        $kDate = hmacsha256($kSecret, $dateStamp);
        $kRegion = hmacsha256($kDate, $regionName);
        $kService = hmacsha256($kRegion, $serviceName);
        $kSigning = hmacsha256($kService, 'aws4_request');

        return $kSigning;
    }

//
//static byte[] getSignatureKey(String key, String dateStamp, String regionName, String serviceName) throws Exception {
//    byte[] kSecret = ("AWS4" + key).getBytes("UTF8");
//       byte[] kDate = HmacSHA256(dateStamp, kSecret);
//       byte[] kRegion = HmacSHA256(regionName, kDate);
//       byte[] kService = HmacSHA256(serviceName, kRegion);
//       byte[] kSigning = HmacSHA256("aws4_request", kService);
//       return kSigning;
//   }
//
//   public void createUploadPolicy(S3FileUploadRequest request, int minSize, int maxSize, int expiryInMinutes) throws Exception {
//    Date signingDate = new Date();
//       String signingCredential = awsUser.getAwsAccessKeyId() + "/" + dateFormat.format(signingDate) + "/" + awsRegion + "/s3/aws4_request";
//
//       String newPolicy = policyTemplate.replace("%CREDENTIAL%", signingCredential);
//       newPolicy = newPolicy.replace("%KEY%", request.getKey());
//       newPolicy = newPolicy.replace("%BUCKET%", bucketBasePath);
//       newPolicy = newPolicy.replace("%MIN_SIZE%", Integer.toString(minSize));
//       newPolicy = newPolicy.replace("%MAX_SIZE%", Integer.toString(maxSize));
//       newPolicy = newPolicy.replace("%DATE%", dateFormat.format(signingDate) + "T000000Z");
//
//       Date now = new Date();
//       Calendar cal = Calendar.getInstance();
//       cal.setTime(now);
//       cal.add(Calendar.MINUTE, expiryInMinutes);
//
//       newPolicy = newPolicy.replace("%EXPIRY%", expiryFormat.format(cal.getTime()));
//       System.out.println("policy: " + newPolicy);
//       String base64Policy = Base64.getEncoder().encodeToString(newPolicy.getBytes("UTF8"));
//
//       byte[] signingKey = getSignatureKey(awsUser.getSecretKey(), dateFormat.format(signingDate), awsRegion, "s3");
//       byte[] signature = HmacSHA256(base64Policy, signingKey);
//       char[] signatureChars = Hex.encodeHex(signature);
//
//       request.setAcl("public-read");
//       request.setCredential(signingCredential);
//       request.setPolicy(base64Policy);
//       request.setSignature(new String(signatureChars));
//       request.setSignatureDate(dateFormat.format(signingDate) + "T000000Z");
//   }
}
