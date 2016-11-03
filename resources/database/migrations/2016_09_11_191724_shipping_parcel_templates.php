<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ShippingParcelTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_parcel_templates', function(Blueprint $table) {
            $table->increments('id');
            $table->string('parcel_id');
            $table->enum('parcel_carrier', ['usps'])->default('usps');
            $table->string('name');
            $table->decimal('length', 10, 4);
            $table->decimal('width', 10, 4);
            $table->decimal('height', 10, 4);
            $table->enum('distance_unit', ['cm', 'in', 'ft', 'mm', 'm', 'yd'])->default('in');
            $table->string('image')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->softDeletes();
        });
//
//        $usps = array (
//            0 =>
//                array (
//                    'token' => 'USPS_FlatRateCardboardEnvelope',
//                    'name' => 'Flat Rate Cardboard Envelope',
//                    'dimensions' => '12.50 x 9.50 x 0.75 in',
//                ),
//            1 =>
//                array (
//                    'token' => 'USPS_FlatRateEnvelope',
//                    'name' => 'Flat Rate Envelope',
//                    'dimensions' => '12.50 x 9.50 x 0.75 in',
//                ),
//            2 =>
//                array (
//                    'token' => 'USPS_FlatRateGiftCardEnvelope',
//                    'name' => 'Flat Rate Gift Card Envelope',
//                    'dimensions' => '10.00 x 7.00 x 0.75 in',
//                ),
//            3 =>
//                array (
//                    'token' => 'USPS_FlatRateLegalEnvelope',
//                    'name' => 'Flat Rate Legal Envelope',
//                    'dimensions' => '15.00 x 9.50 x 0.75 in',
//                ),
//            4 =>
//                array (
//                    'token' => 'USPS_FlatRatePaddedEnvelope',
//                    'name' => 'Flat Rate Padded Envelope',
//                    'dimensions' => '12.50 x 9.50 x 1.00 in',
//                ),
//            5 =>
//                array (
//                    'token' => 'USPS_FlatRateWindowEnvelope',
//                    'name' => 'Flat Rate Window Envelope',
//                    'dimensions' => '10.00 x 5.00 x 0.75 in',
//                ),
//            6 =>
//                array (
//                    'token' => 'USPS_IrregularParcel',
//                    'name' => 'Irregular Parcel',
//                    'dimensions' => '0.00 x 0.00 x 0.00 in',
//                    'active' => false,
//                ),
//            7 =>
//                array (
//                    'token' => 'USPS_LargeFlatRateBoardGameBox',
//                    'name' => 'Large Flat Rate Board Game Box',
//                    'dimensions' => '24.06 x 11.88 x 3.13 in',
//                ),
//            8 =>
//                array (
//                    'token' => 'USPS_LargeFlatRateBox',
//                    'name' => 'Large Flat Rate Box',
//                    'dimensions' => '12.25 x 12.25 x 6.00 in',
//                ),
//            9 =>
//                array (
//                    'token' => 'USPS_LargeVideoFlatRateBox',
//                    'name' => 'Flat Rate Large Video Box (Int\'l only)',
//                'dimensions' => '9.60 x 6.40 x 2.20 in',
//                    'active' => false,
//              ),
//              10 =>
//              array (
//                'token' => 'USPS_MediumFlatRateBox1',
//                'name' => 'Medium Flat Rate Box 1',
//                'dimensions' => '11.25 x 8.75 x 6.00 in',
//              ),
//              11 =>
//              array (
//                'token' => 'USPS_MediumFlatRateBox2',
//                'name' => 'Medium Flat Rate Box 2',
//                'dimensions' => '14.00 x 12.00 x 3.50 in',
//              ),
//              12 =>
//              array (
//                'token' => 'USPS_RegionalRateBoxA1',
//                'name' => 'Regional Rate Box A1',
//                'dimensions' => '10.13 x 7.13 x 5.00 in',
//              ),
//              13 =>
//              array (
//                'token' => 'USPS_RegionalRateBoxA2',
//                'name' => 'Regional Rate Box A2',
//                'dimensions' => '13.06 x 11.06 x 2.50 in',
//              ),
//              14 =>
//              array (
//                'token' => 'USPS_RegionalRateBoxB1',
//                'name' => 'Regional Rate Box B1',
//                'dimensions' => '12.25 x 10.50 x 5.50 in',
//              ),
//              15 =>
//              array (
//                'token' => 'USPS_RegionalRateBoxB2',
//                'name' => 'Regional Rate Box B2',
//                'dimensions' => '16.25 x 14.50 x 3.00 in',
//              ),
//              16 =>
//              array (
//                'token' => 'USPS_SmallFlatRateBox',
//                'name' => 'Small Flat Rate Box',
//                'dimensions' => '8.69 x 5.44 x 1.75 in',
//              ),
//              17 =>
//              array (
//                'token' => 'USPS_SmallFlatRateEnvelope',
//                'name' => 'Small Flat Rate Envelope',
//                'dimensions' => '10.00 x 6.00 x 4.00 in',
//              ),
//            );
//
//        foreach ($usps as $row) {
//            preg_match("/(\d+\.\d{1,2}) x (\d+\.\d{1,2}) x (\d+\.\d{1,2})/", $row['dimensions'], $dimensions);
//
//            $p = new \Kabooodle\Models\ShippingParcelTemplates;
//            $p->parcel_carrier = 'usps';
//            $p->parcel_id = $row['token'];
//            $p->name = $row['name'];
//            $p->length = $dimensions[1];
//            $p->width = $dimensions[2];
//            $p->height = $dimensions[3];
//            $p->active = isset($row['active']) ? $row['active'] : true;
//            $p->save();
//        }

        $sql = "INSERT INTO `shipping_parcel_templates` (`id`, `parcel_id`, `parcel_carrier`, `name`, `length`, `width`, `height`, `distance_unit`, `image`, `active`, `deleted_at`)
VALUES
	(1, 'USPS_FlatRateCardboardEnvelope', 'usps', 'Flat Rate Cardboard Envelope', 12.5000, 9.5000, 0.7500, 'in', 'https://www.usps.com/stamp-collecting/assets/images/aEP_14-F-01-main-278x212.jpg', 1, NULL),
	(2, 'USPS_FlatRateEnvelope', 'usps', 'Flat Rate Envelope', 12.5000, 9.5000, 0.7500, 'in', 'https://www.usps.com/stamp-collecting/assets/images/aEP_14-F-01-main-278x212.jpg', 1, NULL),
	(3, 'USPS_FlatRateGiftCardEnvelope', 'usps', 'Flat Rate Gift Card Envelope', 10.0000, 7.0000, 0.7500, 'in', 'https://www.usps.com/stamp-collecting/assets/images/aEP14GTV-01-main-278x195.jpg', 0, NULL),
	(4, 'USPS_FlatRateLegalEnvelope', 'usps', 'Flat Rate Legal Envelope', 15.0000, 9.5000, 0.7500, 'in', 'https://www.usps.com/stamp-collecting/assets/images/aEP14L-01-main-278x176.jpg', 0, NULL),
	(5, 'USPS_FlatRatePaddedEnvelope', 'usps', 'Flat Rate Padded Envelope', 12.5000, 9.5000, 1.0000, 'in', 'https://www.usps.com/stamp-collecting/assets/images/aEP14PE-01-main-278x223.jpg', 1, NULL),
	(6, 'USPS_FlatRateWindowEnvelope', 'usps', 'Flat Rate Window Envelope', 10.0000, 5.0000, 0.7500, 'in', 'https://www.usps.com/stamp-collecting/assets/images/aEP_14-H-01-main-278x139.jpg', 1, NULL),
	(7, 'USPS_IrregularParcel', 'usps', 'Irregular Parcel', 0.0000, 0.0000, 0.0000, 'in', NULL, 0, NULL),
	(8, 'USPS_LargeFlatRateBoardGameBox', 'usps', 'Large Flat Rate Board Game Box', 24.0600, 11.8800, 3.1300, 'in', 'https://www.usps.com/stamp-collecting/assets/images/aGB-FRB-01-main-278x167.jpg', 1, NULL),
	(9, 'USPS_LargeFlatRateBox', 'usps', 'Large Flat Rate Box', 12.2500, 12.2500, 6.0000, 'in', 'https://www.usps.com/stamp-collecting/assets/images/aLARGE-FRB-01-main-278x147.jpg', 1, NULL),
	(10, 'USPS_LargeVideoFlatRateBox', 'usps', 'Flat Rate Large Video Box (Int\'l only)', 9.6000, 6.4000, 2.2000, 'in', NULL, 0, NULL),
	(11, 'USPS_MediumFlatRateBox1', 'usps', 'Medium Flat Rate Box 1', 11.2500, 8.7500, 6.0000, 'in', 'https://www.usps.com/stamp-collecting/assets/images/aO-FRB1-01-main-278x160.jpg', 1, NULL),
	(12, 'USPS_MediumFlatRateBox2', 'usps', 'Medium Flat Rate Box 2', 14.0000, 12.0000, 3.5000, 'in', 'https://www.usps.com/stamp-collecting/assets/images/aO-FRB2-01-main-278x243.jpg', 1, NULL),
	(13, 'USPS_RegionalRateBoxA1', 'usps', 'Regional Rate Box A1', 10.1300, 7.1300, 5.0000, 'in', 'https://www.usps.com/stamp-collecting/assets/images/RRB_A1X-S0.jpg', 1, NULL),
	(14, 'USPS_RegionalRateBoxA2', 'usps', 'Regional Rate Box A2', 13.0600, 11.0600, 2.5000, 'in', 'https://www.usps.com/stamp-collecting/assets/images/RRB_A2X-L0.jpg', 1, NULL),
	(15, 'USPS_RegionalRateBoxB1', 'usps', 'Regional Rate Box B1', 12.2500, 10.5000, 5.5000, 'in', 'https://www.usps.com/stamp-collecting/assets/images/RRB_B1X-L0.jpg', 1, NULL),
	(16, 'USPS_RegionalRateBoxB2', 'usps', 'Regional Rate Box B2', 16.2500, 14.5000, 3.0000, 'in', 'https://www.usps.com/stamp-collecting/assets/images/RRB_B2X-S0.jpg', 1, NULL),
	(17, 'USPS_SmallFlatRateBox', 'usps', 'Small Flat Rate Box', 8.6900, 5.4400, 1.7500, 'in', 'https://www.usps.com/stamp-collecting/assets/images/aO-SMALL-FRB-01-main-278x203.jpg', 1, NULL),
	(18, 'USPS_SmallFlatRateEnvelope', 'usps', 'Small Flat Rate Envelope', 10.0000, 6.0000, 4.0000, 'in', 'https://www.usps.com/stamp-collecting/assets/images/aEP_14-B-01-main-278x167.jpg', 1, NULL);
";

        DB::statement(DB::raw($sql));

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
