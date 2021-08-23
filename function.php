// Product Admin Custom Field
// Add Meta container admin product pages - Product
add_action( 'add_meta_boxes', 'mv_add_meta_boxes' );
if ( ! function_exists( 'mv_add_meta_boxes' ) )
{
    function mv_add_meta_boxes()
    {
        add_meta_box( 'mv_other_fields', __('Vendor','woocommerce'), 'mv_add_other_fields_for_packaging', 'product', 'side', 'core' );
    }
}

// Add Meta field in the meta container admin product pages - Product
if ( ! function_exists( 'mv_add_other_fields_for_packaging' ) )
{
    function mv_add_other_fields_for_packaging()
    {
        global $post;

        $meta_field_data = get_post_meta( $post->ID, '_my_field_slug', true ) ? get_post_meta( $post->ID, '_my_field_slug', true ) : '';

        echo '<input type="hidden" name="mv_other_meta_field_nonce" value="' . wp_create_nonce() . '">
        <p style="border-bottom:solid 1px #eee;padding-bottom:13px;">
            <input type="text" style="width:250px;";" name="my_field_name" placeholder="' . $meta_field_data . '" value="' . $meta_field_data . '"></p>';

    }
}

// Save the data of the Meta field //Save Function
add_action( 'save_post', 'save_wc_order_other_fields', 10, 1 );
if ( ! function_exists( 'save_wc_order_other_fields' ) )
{

    function save_wc_order_other_fields( $post_id ) {

        // Verification.

        // Check if the nonce is set.
        if ( ! isset( $_POST[ 'mv_other_meta_field_nonce' ] ) ) {
            return $post_id;
        }
        $nonce = $_REQUEST[ 'mv_other_meta_field_nonce' ];

        //Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce ) ) {
            return $post_id;
        }

        // If this is an autosave, the form has not been submitted, so do nothing.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        // Check the user's permissions.
        if ( 'page' == $_POST[ 'post_type' ] ) {

            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {

            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }
        // --- Safe to save the data ! --- //

        // Update the meta field in the database.
        update_post_meta( $post_id, '_my_field_slug', $_POST[ 'v' ] );
    }
}

// Product Admin Custom Column
//start column
add_filter( 'manage_edit-product_columns', 'MY_COLUMNS_FUNCTION' );
function MY_COLUMNS_FUNCTION( $columns ) {
    $new_columns = ( is_array( $columns ) ) ? $columns : array();
    unset( $new_columns[ 'order_actions' ] );
   
    //edit this for your column(s)
    //all of your columns will be added before the actions column
    $new_columns['MY_COLUMN_ID_1'] = 'Vendor';
   
    //stop editing
    $new_columns[ 'order_actions' ] = $columns[ 'order_actions' ];
    return $new_columns;
}

//Order function
add_filter( "manage_edit-product_sortable_columns", 'MY_COLUMNS_SORT_FUNCTION' );
function MY_COLUMNS_SORT_FUNCTION( $columns )
{
    $custom = array(
            'MY_COLUMN_ID_1'    => 'my_field_name',
           
            );
    return wp_parse_args( $custom, $columns );
}
//Parse function
add_action( 'manage_product_posts_custom_column', 'MY_COLUMNS_VALUES_FUNCTION', 2 );
function MY_COLUMNS_VALUES_FUNCTION( $column ) {
    global $post;
    global $meta_field_data;

    $meta_field_data = get_post_meta( $post->ID, '_my_field_slug', true ) ? get_post_meta( $post->ID, '_my_field_slug', true ) : '';

    if ( $column == 'MY_COLUMN_ID_1' ) {
        echo (isset ($meta_field_data)? $meta_field_data: '');
    }
   
}
