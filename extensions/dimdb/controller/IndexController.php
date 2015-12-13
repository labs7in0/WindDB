<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('WIND:cache.strategy.WindDbCache');

class IndexController extends PwBaseController
{
    private function curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $result;
    }

    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
    }

    public function run()
    {
        $id = $this->getInput('id');
        $type = $this->getInput('type');

        $cache = new WindDbCache(Wind::getComponent('db'), array('table-name' => 'app_dimdb_cache', 'field-key' => 'cache_key', 'field-value' => 'cache_value', 'field-expire' => 'cache_expire', 'expires' => '3600'));

        $cache->clear();

        $result = $cache->get($type . '_' . $id);

        if (!$result) {
            switch ($type) {
                case 'book':
                    // 书籍
                    $url = 'https://api.douban.com/v2/book/' . $id;
                    if (!empty(Wekit::C('site', 'app.dimdb.doubankey'))) {
                        $url .= '?apikey=' . Wekit::C('site', 'app.dimdb.doubankey');
                    }

                    $res = $this->curl($url);
                    break;
                case 'movie':
                    // 影视
                    $url = 'https://api.douban.com/v2/movie/subject/' . $id;
                    if (!empty(Wekit::C('site', 'app.dimdb.doubankey'))) {
                        $url .= '?apikey=' . Wekit::C('site', 'app.dimdb.doubankey');
                    }

                    $result = $this->curl($url);

                    $res = array(
                        'title' => $result['title'],
                        'year' => $result['year'],
                        'genres' => $result['genres'],
                        'directors' => array(),
                        'writers' => array(),
                        'actors' => array(),
                        'summary' => $result['summary'],
                        'languages' => $result['languages'],
                        'country' => $result['countries'],
                        'aka' => $result['aka'],
                        'alt' => $result['alt'],
                        'image' => $result['images']['large'],
                    );

                    if (is_array($result['directors'])) {
                        foreach ($result['directors'] as $p) {
                            $res['directors'][] = $p['name'];
                        }
                    }

                    if (is_array($result['writers'])) {
                        foreach ($result['writers'] as $p) {
                            $res['writers'][] = $p['name'];
                        }
                    }

                    if (is_array($result['casts'])) {
                        foreach ($result['casts'] as $p) {
                            $res['actors'][] = $p['name'];
                        }
                    }
                    break;
                case 'music':
                    // 音乐
                    $url = 'https://api.douban.com/v2/music/' . $id;
                    if (!empty(Wekit::C('site', 'app.dimdb.doubankey'))) {
                        $url .= '?apikey=' . Wekit::C('site', 'app.dimdb.doubankey');
                    }

                    $res = $this->curl($url);
                    break;
                default:
                    $url = 'http://omdbapi.com/?i=' . $id;

                    $result = $this->curl($url);

                    $res = array(
                        'title' => $result['Title'],
                        'year' => $result['Year'],
                        'genres' => explode(', ', $result['Genre']),
                        'directors' => explode(', ', $result['Director']),
                        'writers' => explode(', ', $result['Writer']),
                        'actors' => explode(', ', $result['Actors']),
                        'summary' => $result['Plot'],
                        'languages' => explode(', ', $result['Language']),
                        'country' => explode(', ', $result['Country']),
                        'aka' => array(),
                        'alt' => 'http://imdb.com/title/' . $id,
                    );

                    if (!empty(Wekit::C('site', 'app.dimdb.omdbkey'))) {
                        $res['image'] = 'http://img.omdbapi.com/?i=' . $id . '&apikey=' . Wekit::C('site', 'app.dimdb.omdbkey');
                    }
            }

            $result = json_encode($res);
            $cache->set($type . '_' . $id, $result);
        }

        exit($result);
    }
}
