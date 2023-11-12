<?php
/**
 *  Version: 1.0.0
 *  Text Domain: WPStylish-wp-cloud
 * @since 1.0.0
 */
use Aws\S3\S3Client;

class VideoPro_CLOUD_AMAZON_UPLOAD
{

    public function __construct()
    {
        // requere library
        require_once(WP_CLOUD_DIR . 'lib/amazons3/aws-autoloader.php');

        add_action('wp_ajax_amazon-get-bucket', array($this, 'amazon_get_bucket'));
        add_action('wp_ajax_non_priv_amazon-get-bucket', array($this, 'amazon_get_bucket'));
    }

    // get amazon bucket when choose in the uploader
    function amazon_get_bucket()
    {
        $client = $this->create_client();
        $buckets = $client->listBuckets();
        if (!empty($buckets)) {
            wp_send_json_success($buckets['Buckets']);
        } else {
            wp_send_json_error();
        }
        die(0);
    }

    function amazon_get_all_buckets()
    {
        $options = get_option('wp_cloud', array());
        $amazon_s3_access_key = isset($options['amazon_s3_access_key']) ? $options['amazon_s3_access_key'] : null;
        $amazon_s3_access_secret = isset($options['amazon_s3_access_secret']) ? $options['amazon_s3_access_secret'] : null;
        $amazon_s3_region = isset($options['amazon_s3_region']) ? $options['amazon_s3_region'] : null;

        if (empty($amazon_s3_access_key) || empty($amazon_s3_access_secret) || empty($amazon_s3_region)) {
            return false;
        }

        $client = $this->create_client();
        $buckets = $client->listBuckets();

        return $buckets;
    }

    // create amazon credential to access bucket and upload files
    function create_client()
    {
        $options = get_option('wp_cloud', array());
        $amazon_s3_access_key = isset($options['amazon_s3_access_key']) ? $options['amazon_s3_access_key'] : null;
        $amazon_s3_access_secret = isset($options['amazon_s3_access_secret']) ? $options['amazon_s3_access_secret'] : null;
        $amazon_s3_region = isset($options['amazon_s3_region']) ? $options['amazon_s3_region'] : null;
        if ($amazon_s3_access_key && $amazon_s3_access_secret && $amazon_s3_region) {
            return S3Client::factory(array(
                'credentials' => array(
                    'key' => $amazon_s3_access_key,
                    'secret' => $amazon_s3_access_secret,
                ),
                'region' => $amazon_s3_region,
                'version' => 'latest',
            ));
        } else {
            return false;
        }
    }

    // Upload action
    function wp_cloud_amazonS3_action($bucket, $post_id)
    {
        $video_file_url = get_post_meta($post_id, 'tm_video_file', true);

        $content = file_get_contents($video_file_url);
        $path_parts = pathinfo($video_file_url);
        $fileName = $path_parts['basename'];
        $fileType = $path_parts['extension'];

        $client = $this->create_client();
        if ($bucket) {
            $result = $client->putObject(
                array(
                    'Bucket' => $bucket,
                    'Key' => $fileName,
                    'ACL' => 'public-read',
                    'ContentType' => $fileType,
                    'Body' => $content
                )
            );
            $file_info = array();
            $file_info['fileURL'] = $video_file_url;
            $file_info['fileName'] = $fileName;
            $file_info['fileType'] = $fileType;
            $file_info['ObjectURL'] = $result['ObjectURL'];
            return $file_info;
        }
    }
}

$GLOBALS['videoPro_cloud_amazon_upload'] = new VideoPro_CLOUD_AMAZON_UPLOAD();

