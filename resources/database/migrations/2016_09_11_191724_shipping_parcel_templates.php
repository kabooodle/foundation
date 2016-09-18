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
            $table->softDeletes();
        });

        $usps = array (
            0 =>
                array (
                    'token' => 'USPS_FlatRateCardboardEnvelope',
                    'name' => 'Flat Rate Cardboard Envelope',
                    'dimensions' => '12.50 x 9.50 x 0.75 in',
                ),
            1 =>
                array (
                    'token' => 'USPS_FlatRateEnvelope',
                    'name' => 'Flat Rate Envelope',
                    'dimensions' => '12.50 x 9.50 x 0.75 in',
                ),
            2 =>
                array (
                    'token' => 'USPS_FlatRateGiftCardEnvelope',
                    'name' => 'Flat Rate Gift Card Envelope',
                    'dimensions' => '10.00 x 7.00 x 0.75 in',
                ),
            3 =>
                array (
                    'token' => 'USPS_FlatRateLegalEnvelope',
                    'name' => 'Flat Rate Legal Envelope',
                    'dimensions' => '15.00 x 9.50 x 0.75 in',
                ),
            4 =>
                array (
                    'token' => 'USPS_FlatRatePaddedEnvelope',
                    'name' => 'Flat Rate Padded Envelope',
                    'dimensions' => '12.50 x 9.50 x 1.00 in',
                ),
            5 =>
                array (
                    'token' => 'USPS_FlatRateWindowEnvelope',
                    'name' => 'Flat Rate Window Envelope',
                    'dimensions' => '10.00 x 5.00 x 0.75 in',
                ),
            6 =>
                array (
                    'token' => 'USPS_IrregularParcel',
                    'name' => 'Irregular Parcel',
                    'dimensions' => '0.00 x 0.00 x 0.00 in',
                ),
            7 =>
                array (
                    'token' => 'USPS_LargeFlatRateBoardGameBox',
                    'name' => 'Large Flat Rate Board Game Box',
                    'dimensions' => '24.06 x 11.88 x 3.13 in',
                ),
            8 =>
                array (
                    'token' => 'USPS_LargeFlatRateBox',
                    'name' => 'Large Flat Rate Box',
                    'dimensions' => '12.25 x 12.25 x 6.00 in',
                ),
            9 =>
                array (
                    'token' => 'USPS_LargeVideoFlatRateBox',
                    'name' => 'Flat Rate Large Video Box (Int\'l only)',
                'dimensions' => '9.60 x 6.40 x 2.20 in',
              ),
              10 =>
              array (
                'token' => 'USPS_MediumFlatRateBox1',
                'name' => 'Medium Flat Rate Box 1',
                'dimensions' => '11.25 x 8.75 x 6.00 in',
              ),
              11 =>
              array (
                'token' => 'USPS_MediumFlatRateBox2',
                'name' => 'Medium Flat Rate Box 2',
                'dimensions' => '14.00 x 12.00 x 3.50 in',
              ),
              12 =>
              array (
                'token' => 'USPS_RegionalRateBoxA1',
                'name' => 'Regional Rate Box A1',
                'dimensions' => '10.13 x 7.13 x 5.00 in',
              ),
              13 =>
              array (
                'token' => 'USPS_RegionalRateBoxA2',
                'name' => 'Regional Rate Box A2',
                'dimensions' => '13.06 x 11.06 x 2.50 in',
              ),
              14 =>
              array (
                'token' => 'USPS_RegionalRateBoxB1',
                'name' => 'Regional Rate Box B1',
                'dimensions' => '12.25 x 10.50 x 5.50 in',
              ),
              15 =>
              array (
                'token' => 'USPS_RegionalRateBoxB2',
                'name' => 'Regional Rate Box B2',
                'dimensions' => '16.25 x 14.50 x 3.00 in',
              ),
              16 =>
              array (
                'token' => 'USPS_SmallFlatRateBox',
                'name' => 'Small Flat Rate Box',
                'dimensions' => '8.69 x 5.44 x 1.75 in',
              ),
              17 =>
              array (
                'token' => 'USPS_SmallFlatRateEnvelope',
                'name' => 'Small Flat Rate Envelope',
                'dimensions' => '10.00 x 6.00 x 4.00 in',
              ),
            );

        foreach ($usps as $row) {
            preg_match("/(\d+\.\d{1,2}) x (\d+\.\d{1,2}) x (\d+\.\d{1,2})/", $row['dimensions'], $dimensions);

            $p = new \Kabooodle\Models\ShippingParcelTemplates;
            $p->parcel_carrier = 'usps';
            $p->parcel_id = $row['token'];
            $p->name = $row['name'];
            $p->length = $dimensions[1];
            $p->width = $dimensions[2];
            $p->height = $dimensions[3];
            $p->save();
        }
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
