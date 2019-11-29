<?php

namespace ColdTurkey\DaysOnMarket;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly.

// Composer autoloader
require_once DAYS_MARKET_PLUGIN_PATH . 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class FrontDesk
{

    protected $token;
    protected $api_key;
    protected $api_version;
    protected $api_base;
    protected $guzzle;

    /**
     * Basic constructor for the FrontDesk class
     *
     * @param int $api_version
     */
    public function __construct($api_version = 3)
    {
        $this->token = 'pf_days_on_market';
        $this->api_key = get_option('pf_seller_quiz_frontdesk_key');
        if (get_option('pf_frontdesk_key')) {
            $this->api_key = get_option('pf_frontdesk_key');
        }
        $this->api_version = $api_version;
        $this->api_base = 'https://platformcrm.com/api/v' . $api_version . '/';
        $this->guzzle = new Client();

        // Display admin notices when required
        add_action('admin_notices', [$this, 'adminNotices']);
    }

    /**
     * Create a campaign on tryfrontdesk.com
     * using the given data.
     *
     * @param string $title
     * @param string $permalink
     */
    public function createCampaign($title, $permalink)
    {
        try {
            if ($this->api_key != null || $this->api_key != '') {
                $response = $this->guzzle->post($this->api_base . 'campaigns/', [
                    'form_params' => [
                        'title'       => $title,
                        'description' => 'Campaign for Platform Days on Market Funnel',
                        'type'        => 'Platform',
                        'total_cost'  => '10000',
                        'source'      => $permalink
                    ],
                    'headers'     => [
                        'Authorization' => 'Bearer ' . $this->api_key,
                    ],
                ]);

                add_filter('redirect_post_location', [$this, 'add_success_var'], 99);

                return json_decode($response->getBody(), true)['data']['id'];
            }
        } catch (RequestException $e) {
            add_filter('redirect_post_location', [$this, 'add_error_var'], 99);
        }
    }

    /**
     * Update an existing FrontDesk campaign
     * with a new title or permalink.
     *
     * @param $id
     * @param $title
     * @param $permalink
     */
    public function updateCampaign($id, $title, $permalink)
    {
        if ($this->api_key != null || $this->api_key != '') {
            $this->guzzle->patch($this->api_base . 'campaigns/' . $id, [
                'form_params' => [
                    'title'  => $title,
                    'source' => $permalink
                ],
                'headers'     => [
                    'Authorization' => 'Bearer ' . $this->api_key,
                ],
            ]);
        }
    }

    /**
     * Create a prospect on tryfrontdesk.com
     * using the given data.
     *
     * @param array $data
     *
     * @return json|null
     */
    public function createProspect($data)
    {
        try {
            if ($this->api_key != null || $this->api_key != '') {
                $response = $this->guzzle->post($this->api_base . 'subscribers/', [
                    'form_params' => [
                        'campaigns'  => $data['campaign_id'],
                        'email'      => $data['email'],
                        'first_name' => $data['first_name']
                    ],
                    'headers'     => [
                        'Authorization' => 'Bearer ' . $this->api_key,
                    ],
                ]);

                return json_decode($response->getBody(), true)['data']['id'];
            }

            return null;
        } catch (RequestException $e) {
            return null;
        }
    }

    /**
     * Update an existing prospect on tryfrontdesk.com
     * using the given data.
     *
     * @param int $id
     * @param array $data
     *
     * @return json|null
     */
    public function updateProspect($id, $data)
    {
        try {
            if ($this->api_key != null || $this->api_key != '') {
                $response = $this->guzzle->patch($this->api_base . 'subscribers/' . $id, [
                    'form_params' => [
                        'email'     => $data['email'],
                        'last_name' => $data['last_name'],
                        'address'   => $data['address'],
                        'address_2' => $data['address_2'],
                        'city'      => $data['city'],
                        'state'     => $data['state'],
                        'zip_code'  => $data['zip_code'],
                        'phone'     => $data['phone']
                    ],
                    'headers'     => [
                        'Authorization' => 'Bearer ' . $this->api_key,
                    ],
                ]);

                return json_decode($response->getBody(), true)['data']['id'];
            }

            return null;
        } catch (RequestException $e) {
            return null;
        }
    }

    /**
     * Create a note for an existing FrontDesk prospect
     *
     * @param $id
     * @param $title
     * @param $content
     *
     * @return null
     */
    public function createNote($id, $title, $content)
    {
        try {
            if ($this->api_key != null || $this->api_key != '') {
                $response = $this->guzzle->post($this->api_base . 'subscribers/notes/', [
                    'form_params' => [
                        'subscriber_id' => $id,
                        'title'         => $title,
                        'content'       => $content
                    ],
                    'headers'     => [
                        'Authorization' => 'Bearer ' . $this->api_key,
                    ],
                ]);

                return json_decode($response->getBody(), true)['data']['id'];
            }

            return null;
        } catch (RequestException $e) {
            return null;
        }
    }

    /**
     * Pass a success notification while
     * in the admin panel if necessary
     *
     * @param string $location
     *
     * @return mixed
     */
    public function add_success_var($location)
    {
        remove_filter('redirect_post_location', [$this, 'add_success_var'], 99);

        return esc_url_raw(add_query_arg([$this->token . '_frontdesk_success' => true], $location));
    }

    /**
     * Pass a error notification while
     * in the admin panel if necessary
     *
     * @param string $location
     *
     * @return mixed
     */
    public function add_error_var($location)
    {
        remove_filter('redirect_post_location', [$this, 'add_error_var'], 99);

        return esc_url_raw(add_query_arg([$this->token . '_frontdesk_error' => true], $location));
    }

    /**
     * Define the different administrator notices
     * that may be displayed to the user if necessary.
     *
     * @return string
     */
    public function adminNotices()
    {
        if (isset($_GET[$this->token . '_frontdesk_error'])) {
            echo '<div class="error"><p>A Campaign with this URL already exists. No new Platform CRM Campaign has been created.</p></div>';
        }

        if (isset($_GET[$this->token . '_frontdesk_success'])) {
            echo '<div class="updated"><p>A Campaign for this House Hunter has been successfully setup on Platform CRM!</p></div>';
        }
    }

}