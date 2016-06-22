<?php

namespace Aruna;

/**
 * Class ProcessWebmentionsAction
 * @author yourname
 */
class ProcessWebmentionsAction
{

    public function __construct(
        $log,
        $eventStore,
        $http,
        $mentionsRepositoryWriter
    ) {
        $this->log = $log;
        $this->eventStore = $eventStore;
        $this->http = $http;
        $this->mentionsRepositoryWriter = $mentionsRepositoryWriter;
    }

    public function __invoke()
    {
        $this->log->info("Processing webmentions");
        $count = 0;
        foreach ($this->eventStore->findByExtension('webmentions', 'json') as $mention_file) {

            $mention = json_decode($this->eventStore->readContents($mention_file['path']), true);
            try {
                $mention = (new VerifyWebmentionRequest())->__invoke($mention);
            } catch (\Exception $e) {
                $this->log->error(sprintf("Invalid Webmention: %s\n", $e->getMessage()));
                $this->eventStore->delete($mention_file['path']);
                continue;
            }

            try {
                // verify
                $mention_html = $this->verifyWebmention($mention);

                // save html
                $mention['id'] = md5($mention['source'].$mention['target']);
                $file_path = "processed_webmentions/".$mention['id'].".html";
                $this->eventStore->save($file_path, $mention_html);

                // cache to db
                $mention_view_model = new \Aruna\PostViewModel(\Mf2\parse($mention_html));
                $this->mentionsRepositoryWriter->save(
                    $mention['id'],
                    basename($mention['target']),
                    $mention_view_model
                );

                // notify
                $m = sprintf(
                    '%s %s %s: %s',
                    $mention_view_model->author()['name'],
                    $mention_view_model->type(),
                    $mention['target'],
                    $mention_view_model->get("url")
                );
                $this->log->notice($m);
            } catch (\Exception $e) {
                $this->log->error(sprintf("Invalid Webmention: %s\n", $e->getMessage()));
                $this->eventStore->delete($mention_file['path']);
                continue;
            }

            $this->eventStore->delete($mention_file['path']);

            $count += 1;
            if ($count > 10) {
                exit;
            }
        }
    }

    private function verifyWebmention(array $mention)
    {
        $mention_html = $this->fetchMentionHtml($mention['source']);
        $this->htmlContainsLink($mention_html, $mention['target']);
        return $mention_html;
    }
    private function fetchMentionHtml($url)
    {
        $this->log->info(sprintf("Fetching HTML from %s", $url));
        $result = $this->http->request("GET", $url);
        return (string) $result->getBody();
    }
    private function loadDOM($html)
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        try {
            $dom->loadHTML($html);
        } catch (\Exception $e) {
        }
        return $dom;
    }
    private function htmlContainsLink($html, $url)
    {
        $xpath = new \DOMXpath($this->loadDOM($html));
        foreach ($xpath->query('//a') as $link) {
            if ($link->getAttribute("href") == $url) {
                return true;
            }
        }
        throw new \Exception(sprintf("Source html does not contain link to target [%s]", $url));
    }
}
