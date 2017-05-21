<?php

namespace Admin\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/hom")
     */
    public function indexAction()
    {
        $oembed_endpoint = 'http://vimeo.com/api/oembed';

        // Grab the video url from the url, or use default
        $video_url = (isset($_GET['url'])) ? $_GET['url'] : 'http://vimeo.com/7100569';

        // Create the URLs
        $json_url = $oembed_endpoint . '.json?url=' . rawurlencode($video_url) . '&width=640';
        $xml_url = $oembed_endpoint . '.xml?url=' . rawurlencode($video_url) . '&width=640';

        // Load in the oEmbed XML
        $oembed = simplexml_load_string($this->curlGet($xml_url));

        $title       = $oembed->title;
        $author_name = $oembed->author_name;
        $author_url  = $oembed->author_url;
        $video       = html_entity_decode($oembed->html);

        return $this->render(
            'AdminUserBundle:Default:index.html.twig',
            [
                'title'       => $title,
                'author_name' => $author_name,
                'author_url'  => $author_url,
                'video'       => $video
            ]
        );
    }

    // Curl helper function
    public function curlGet($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        $return = curl_exec($curl);
        curl_close($curl);
        return $return;
    }
}
