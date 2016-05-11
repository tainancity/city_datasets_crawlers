<?php

require __DIR__ . '/ckan/vendor/autoload.php';
$config = require __DIR__ . '/config.php';
$targets = array('tainan', 'taoyuan', 'nantou', 'hccg');
foreach ($targets AS $target) {
    $s = Guzzle\Service\Builder\ServiceBuilder::factory($config['sites'])->get($target);

    $datasets = $s->GetDatasets()->getAll();
    $result = array();

    foreach ($datasets['result'] AS $datasetId) {
        $jsonDataset = $s->GetDataset(array('id' => $datasetId))->getAll();
        $org = $jsonDataset['result']['organization']['title'];
        if (empty($org)) {
            $org = $jsonDataset['result']['organization']['id'];
        }
        if (!isset($result[$org])) {
            $result[$org] = array(
                'datasets' => array(),
                'counters' => array(
                    'datasets' => 0,
                    'dataset_total' => 0,
                    'dataset_recent' => 0,
                    'resource_total' => 0,
                    'resource_recent' => 0,
                ),
            );
        }
        $result[$org]['counters']['datasets'] += 1;
        $rowDataset = array(
            'name' => $jsonDataset['result']['name'],
            'title' => $jsonDataset['result']['title'],
            'organization_id' => $jsonDataset['result']['organization']['id'],
            'metadata_created' => $jsonDataset['result']['metadata_created'],
            'metadata_modified' => $jsonDataset['result']['metadata_modified'],
            'timeEnd' => 0,
            'timeBegin' => strtotime($jsonDataset['result']['metadata_created']),
            'resources' => array(),
            'counters' => array(
                'resource_total' => 0,
                'resource_recent' => 0,
            ),
        );
        $rowDataset['timeEnd'] = strtotime($jsonDataset['result']['metadata_modified']);
        foreach ($jsonDataset['result']['resources'] AS $resource) {
            $t = strtotime($resource['created']);
            if ($t > $rowDataset['timeEnd']) {
                $rowDataset['timeEnd'] = $t;
            }
            $t = strtotime($resource['webstore_last_updated']);
            if ($t > $rowDataset['timeEnd']) {
                $rowDataset['timeEnd'] = $t;
            }
            $t = strtotime($resource['last_modified']);
            if ($t > $rowDataset['timeEnd']) {
                $rowDataset['timeEnd'] = $t;
            }
            $rowResource = array(
                'id' => $resource['id'],
                'name' => $resource['name'],
                'created' => $resource['created'],
                'webstore_last_updated' => $resource['webstore_last_updated'],
                'last_modified' => $resource['last_modified'],
            );
            $result[$org]['counters']['resource_total'] += $resource['tracking_summary']['total'];
            $result[$org]['counters']['resource_recent'] += $resource['tracking_summary']['recent'];
            $rowDataset['counters']['resource_total'] += $resource['tracking_summary']['total'];
            $rowDataset['counters']['resource_recent'] += $resource['tracking_summary']['recent'];
            $rowDataset['resources'][$resource['id']] = $rowResource;
        }
        $result[$org]['datasets'][$jsonDataset['result']['id']] = $rowDataset;
    }
    ksort($result);
    foreach ($result AS $k => $v) {
        ksort($result[$k]);
    }

    $result['time_generated'] = time();

    file_put_contents(__DIR__ . '/datasets/' . $target . '.json', json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}