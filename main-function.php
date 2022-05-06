/**
 * Register practice post type
 */
add_action('init', 'register_practice_cpt');

function register_practice_cpt() {
    register_post_type('practice', [
        'label' => 'Practices', 
        'public' => true,
		'menu_icon' => 'dashicons-admin-home',
        'capability_type' => 'post',
    ]);
}



/**
 * Function to interact with drye eye API
 */

if( ! wp_next_scheduled('update_practice_list') ) {
    wp_schedule_event( time(), 'daily', 'get_practices_from_api' );
}

add_action('wp_ajax_nopriv_get_practices_from_api', 'get_practices_from_api');
add_action('wp_ajax_get_practices_from_api', 'get_practices_from_api');
function get_practices_from_api() {

    $file = get_stylesheet_directory() . '/practices_report.txt';
    $current_page = ( ! empty($_POST['current_page']) ) ? $_POST['current_page'] : 1;


    $practices = [];
    $results = wp_remote_retrieve_body( wp_remote_get('https://api.dryeyerescue.com/practices-wp'));

    file_put_contents($file, "Current page: " . $current_page. "\n\n", FILE_APPEND);




    $results = json_decode($results);

    if( ! is_array( $results ) || empty($results) ){
         return false;
    }

    $practices[] = $results;

    foreach($practices[0] as $practice ) {

        $practice_slug = sanitize_title($practice->name . '-' . $practice->city );
		$treatments = $practice->dryEyeTreatments; /* variable to access the treatments array */
		$services = $practice->eyeCareServices; /* variable to access the services array */
		$tests = $practice->tests;  /* variable to access the equipments array */
		$practiceGallery = $practice->imageGallery; /* variable to access gallery array */
		$practiceProducts = $practice->dryEyeProducts; /* variable to access products array */
		$practiceDoctors = $practice->doctors; /* variable to access the doctors array */

		$products = array(); /* function to create an array that will hold all of the returned products */
		$doctors = array(); /* function to create an array that will hold all of the returned doctors */
		
        /* if the practice already exists we just want to look for updated information */
        $existing_practice = get_page_by_path( $practice_slug, 'OBJECT', 'practice' );

        if ($existing_practice === null ) {

            $inserted_practice =  wp_insert_post([ //function that creates a new post. 
                'post_name' =>  $practice_slug,
                'post_title' => $practice_slug,
                'post_type' => 'practice',
                'post_status' => 'publish',
            ]);
    
            if( is_wp_error( $inserted_practice ) ){
                continue;
            }
			/* General Details */
            $fillable = [
				'field_624ddffe088a5' => 'name',
				'field_624de010088a6' => 'email',
				'field_624de022088a7' => 'phone',
				'field_624de036088a8' => 'phone_tracking_number',
				'field_624de04640d4d' => 'address',
				'field_62695ef815d46' => 'street_number',
				'field_62695f0115d47' => 'route',
				'field_62695f0615d48' => 'suite_number',
				'field_62695b5efb9f9' => 'city',
				'field_62695b3bfb9f7' => 'state_short',
				'field_62695b4ffb9f8' => 'zip',
				'field_624de052a6160' => 'website',
				'field_624df216c01b7' => 'createdAt',
				'field_62545883a95ea' => 'monday_op_hours',
				'field_62545890a95eb' => 'tuesday_op_hours',
				'field_62545897a95ec' => 'wednesday_op_hours',
				'field_6254589ca95ed' => 'thursday_op_hours',
				'field_625458a1a95ee' => 'friday_op_hours',
				'field_625458a6a95ef' => 'saturday_op_hours',
				'field_625458ada95f0' => 'sunday_op_hours',
				'field_62546d50fd182' => 'provider',
				'field_62546d5bfd183' => 'providerPlus',
				'field_62546d67fd184' => 'partner',
			];
			/* Gallery */
			$practiceGallery_fillable = [
				'field_626adbb9113f6' => $practiceGallery[0],
				'field_626ae4ad63569' => $practiceGallery[1],
			];
			/* Treatments */
			$treatments_fillable = [
				'field_62697078a401a' => $treatments[0],
				'field_62697085a401b' => $treatments[1],
				'field_62697089a401c' => $treatments[2],
				'field_6269708ca401d' => $treatments[3],
				'field_6269708ea401e' => $treatments[4],
				'field_62697090a401f' => $treatments[5],
				'field_62697093a4020' => $treatments[6],
				'field_62697094a4021' => $treatments[7],
				'field_62697099a4022' => $treatments[8],
				'field_6269709aa4023' => $treatments[9],
				'field_6269709ca4024' => $treatments[10],
				'field_6269709ea4025' => $treatments[11],
			];
			/* Services */
			$services_fillable = [
				'field_626ac2e33e486' => $services[0],
				'field_626ac2ed3e487' => $services[1],
				'field_626ac2ef3e488' => $services[2],
				'field_626ac2f03e489' => $services[3],
				'field_626ac2f13e48a' => $services[4],
				'field_626ac2f33e48b' => $services[5],
				'field_626ac2f43e48c' => $services[6],
				'field_626ac2f53e48d' => $services[7],
				'field_626ac2f63e48e' => $services[8],
				'field_626ac2f73e48f' => $services[9],
				'field_626ac2f93e490' => $services[10],
				'field_626ac2fa3e491' => $services[11],
				'field_626ac2fb3e492' => $services[12],
				'field_626ac2fc3e493' => $services[13],
				'field_626ac2fd3e494' => $services[14],
			];
			/* Tests */
			$tests_fillable = [
				'field_626ac45a727b4' => $tests[0],
				'field_626ac45f727b5' => $tests[1],
				'field_626ac460727b6' => $tests[2],
				'field_626ac461727b7' => $tests[3],
				'field_626ac462727b8' => $tests[4],
				'field_626ac463727b9' => $tests[5],
				'field_626ac464727ba' => $tests[6],
				'field_626ac465727bb' => $tests[7],
				'field_626ac466727bc' => $tests[8],
				'field_626ac467727bd' => $tests[9],
			];
			/* Products */
			foreach( $practiceProducts as $product ) {
				$products_fillable = [
					'field_62717c28811db' => $product->title,
					'field_62717c2c811dc' => $product->image,
					'field_62717c30811dd' => $product->handle,
				];
				array_push($products, $products_fillable);
            }
			
			update_field( 'field_62717c19811da', $products, $inserted_practice );

			/* Doctors */
			foreach( $practiceDoctors as $doctor ) {
				$doctors_fillable = [
					'field_62752a3f07f81' => $doctor->name,
					'field_62752a4407f82' => $doctor->image,
				];
				array_push($doctors, $doctors_fillable);
            }
			
			update_field( 'field_62752a2c07f80', $doctors, $inserted_practice );

            foreach( $fillable as $key => $name ) {
                update_field( $key, $practice->$name, $inserted_practice );
            }

			foreach( $practiceGallery_fillable as $key => $name ) {
                update_field( $key, $name, $inserted_practice );
            }

			foreach( $treatments_fillable as $key => $name ) {
                update_field( $key, $name, $inserted_practice );
            }

			foreach( $services_fillable as $key => $name ) {
                update_field( $key, $name, $inserted_practice );
            }

			foreach( $tests_fillable as $key => $name ) {
                update_field( $key, $name, $inserted_practice );
            }


        } else {

            $existing_practice_id = $existing_practice->ID;
            $existing_practice_timestamp = $existing_practice = get_field('createdAt', $existing_practice_id);

            if( $practice->createdAt >= $existing_practice_timestamp ) {
                //update our post meta
                $fillable = [
					'field_624ddffe088a5' => 'name',
					'field_624de010088a6' => 'email',
					'field_624de022088a7' => 'phone',
					'field_624de036088a8' => 'phone_tracking_number',
					'field_624de04640d4d' => 'address',
					'field_62695ef815d46' => 'street_number',
					'field_62695f0115d47' => 'route',
					'field_62695f0615d48' => 'suite_number',
					'field_62695b5efb9f9' => 'city',
					'field_62695b3bfb9f7' => 'state_short',
					'field_62695b4ffb9f8' => 'zip',
					'field_624de052a6160' => 'website',
					'field_624df216c01b7' => 'createdAt',
					'field_62545883a95ea' => 'monday_op_hours',
					'field_62545890a95eb' => 'tuesday_op_hours',
					'field_62545897a95ec' => 'wednesday_op_hours',
					'field_6254589ca95ed' => 'thursday_op_hours',
					'field_625458a1a95ee' => 'friday_op_hours',
					'field_625458a6a95ef' => 'saturday_op_hours',
					'field_625458ada95f0' => 'sunday_op_hours',
					'field_62546d50fd182' => 'provider',
					'field_62546d5bfd183' => 'providerPlus',
					'field_62546d67fd184' => 'partner',
	
				];

				$practiceGallery_fillable = [
					'field_626adbb9113f6' => $practiceGallery[0],
					'field_626ae4ad63569' => $practiceGallery[1],
				];

				$treatments_fillable = [
					'field_62697078a401a' => $treatments[0],
					'field_62697085a401b' => $treatments[1],
					'field_62697089a401c' => $treatments[2],
					'field_6269708ca401d' => $treatments[3],
					'field_6269708ea401e' => $treatments[4],
					'field_62697090a401f' => $treatments[5],
					'field_62697093a4020' => $treatments[6],
					'field_62697094a4021' => $treatments[7],
					'field_62697099a4022' => $treatments[8],
					'field_6269709aa4023' => $treatments[9],
					'field_6269709ca4024' => $treatments[10],
					'field_6269709ea4025' => $treatments[11],
				];

				$services_fillable = [
					'field_626ac2e33e486' => $services[0],
					'field_626ac2ed3e487' => $services[1],
					'field_626ac2ef3e488' => $services[2],
					'field_626ac2f03e489' => $services[3],
					'field_626ac2f13e48a' => $services[4],
					'field_626ac2f33e48b' => $services[5],
					'field_626ac2f43e48c' => $services[6],
					'field_626ac2f53e48d' => $services[7],
					'field_626ac2f63e48e' => $services[8],
					'field_626ac2f73e48f' => $services[9],
					'field_626ac2f93e490' => $services[10],
					'field_626ac2fa3e491' => $services[11],
					'field_626ac2fb3e492' => $services[12],
					'field_626ac2fc3e493' => $services[13],
					'field_626ac2fd3e494' => $services[14],
				];

				$tests_fillable = [
					'field_626ac45a727b4' => $tests[0],
					'field_626ac45f727b5' => $tests[1],
					'field_626ac460727b6' => $tests[2],
					'field_626ac461727b7' => $tests[3],
					'field_626ac462727b8' => $tests[4],
					'field_626ac463727b9' => $tests[5],
					'field_626ac464727ba' => $tests[6],
					'field_626ac465727bb' => $tests[7],
					'field_626ac466727bc' => $tests[8],
					'field_626ac467727bd' => $tests[9],
				];

				foreach( $practiceProducts as $product ) {
					$products_fillable = [
						'field_62717c28811db' => $product->title,
						'field_62717c2c811dc' => $product->image,
						'field_62717c30811dd' => $product->handle,
					];
					array_push($products, $products_fillable);
				}
				
				update_field( 'field_62717c19811da', $products, $existing_practice_id );

				/* Doctors */
				foreach( $practiceDoctors as $doctor ) {
				$doctors_fillable = [
					'field_62752a3f07f81' => $doctor->name,
					'field_62752a4407f82' => $doctor->image,
				];
				array_push($doctors, $doctors_fillable);
           		 }
				update_field( 'field_62752a2c07f80', $doctors, $existing_practice_id );
	
				foreach( $fillable as $key => $name ) {
                    update_field( $key, $practice->$name, $existing_practice_id );
                }

				foreach( $practiceGallery_fillable as $key => $name ) {
                    update_field( $key, $name, $existing_practice_id );
                }

				foreach( $treatments_fillable as $key => $name ) {
					update_field( $key, $name, $existing_practice_id );
				}

				foreach( $services_fillable as $key => $name ) {
					update_field( $key, $name, $existing_practice_id );
				}

				foreach( $tests_fillable as $key => $name ) {
					update_field( $key, $name, $existing_practice_id );
				}
            }

        }
    }

    $current_page;

    wp_remote_post( admin_url('admin-ajax.php?action=get_practices_from_api'), [

    'blocking' => false, /* We don't care what it sends back, we just need to make the request and move on */ 
    'sslverify' => false,
    'body' => [
        'current_page' => $current_page
    ]
     ] );
}

/* Gallery Shortcode */
// Shortcode to output custom PHP in Elementor
function elementor_practice_gallery_shortcode( $atts ) {
    
	$image = get_field('image_1');
	$galleryHTML = "<img src=" . $image . ">";

	return $galleryHTML;
	
}
add_shortcode( 'practice_gallery', 'elementor_practice_gallery_shortcode');


/* Address Shortcode */
// Shortcode to output custom PHP in Elementor
function elementor_practice_address_shortcode( $atts ) {
    
	$street_number = get_field('street_number');
	$route = get_field('route');
	$suite_number = get_field('suite_number');
	$city = get_field('city');
	$state = get_field('state');
	$zipcode = get_field('zipcode');

	$addressHTML = "<p>" . $street_number . " " . $route . ", " . $suite_number . "<br>" . $city . ", " . $state . ", " . $zipcode . "</p>";

	return $addressHTML;
	
}
add_shortcode( 'practice_address', 'elementor_practice_address_shortcode');


/* Products Shortcode */
// Shortcode to output custom PHP in Elementor
function elementor_practice_products_shortcode( $atts ) {


	$rows = get_field('field_62717c19811da');
	if($rows)
	{
    echo '<ul class="product-grid">';

    foreach($rows as $row)
    {
        echo '<li class="product-card"><a href=" ' . $row['handle'] . '" target="_blank">' . '<img src=" ' . $row['image'] . ' " alt="Product Image">' . '<h3>' . $row['title'] . '</h3>' . '</a></li>';
    }

    echo '</ul>';
}
	
}
add_shortcode( 'practice_products', 'elementor_practice_products_shortcode');
