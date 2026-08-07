<?php

class RedirectUrl
{
    private function file_get_contents_curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }



    public function getMeta($originalURL)
    {
        try {
            $html = $this->file_get_contents_curl($originalURL);

            //parsing begins here:
            $doc = new DOMDocument();
            @$doc->loadHTML($html);
            $nodes = $doc->getElementsByTagName('title');

            //get and display what you need:
            $title = $nodes->item(0)->nodeValue;

            $metas = $doc->getElementsByTagName('meta');

            for ($i = 0; $i < $metas->length; $i++) {
                $meta = $metas->item($i);
                if ($meta->getAttribute('name') == 'description')
                    $description = $meta->getAttribute('content');

                if ($meta->getAttribute('name') == 'keywords')
                    $keywords = $meta->getAttribute('content');

                if ($meta->getAttribute('property') == 'og:image')
                    $og_image = $meta->getAttribute('content');
            }

            return [
                'title' => $title,
                'description' => $description ?? '',
                'keywords' => $keywords ?? '',
                'og_image' => $og_image ?? '',
                'original_url' => $originalURL
            ];
        } catch (\Throwable $th) {
            // return 'invalid URL';
            // return $th;
        }
    }
}
