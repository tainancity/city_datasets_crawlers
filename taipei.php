<?php

$count = 1;
$page = 1;
$offset = 0;
$limit = 20;
$result = array();
while ($offset < $count) {
    $json = json_decode(file_get_contents("http://data.taipei/opendata/datalist/apiAccess?scope=datasetMetadataSearch&limit={$limit}&offset={$offset}"), true);
    if ($count === 1) {
        $count = $json['result']['count'];
    }
    if (!is_array($json['result']['results'])) {
        continue;
    }
    foreach ($json['result']['results'] AS $dataset) {
        $org = $dataset['orgName'];
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
            'name' => $dataset['id'],
            'title' => $dataset['title'],
            'organization_id' => $dataset['orgId'],
            'metadata_modified' => $dataset['metadata_modified'],
            'timeEnd' => 0,
            'timeBegin' => 0,
            'resources' => array(),
            'counters' => array(
                'resource_total' => 0,
                'resource_recent' => 0,
            ),
        );
        $rowDataset['timeEnd'] = strtotime($dataset['metadata_modified']);
        if(!empty($dataset['issued'])) {
            $rowDataset['timeBegin'] = strtotime($dataset['issued']);
        }
        foreach ($dataset['resources'] AS $resource) {
            if (isset($resource['resourceId'])) {
                if (!isset($resource['resourceName'])) {
                    $resource['resourceName'] = $resource['resourceDescription'];
                }
                $t = strtotime($resource['resourceUpdate']);
                if ($t > $rowDataset['timeEnd']) {
                    $rowDataset['timeEnd'] = $t;
                }
                $rowResource = array(
                    'id' => $resource['resourceId'],
                    'name' => $resource['resourceName'],
                    'created' => date('Y-m-d H:i:s', $t),
                    'last_modified' => $resource['resourceUpdate'],
                );
                $rowDataset['resources'][$resource['resourceId']] = $rowResource;
            }
        }
        $result[$org]['datasets'][$dataset['id']] = $rowDataset;
    }

    ++$page;
    $offset+=$limit;
}
ksort($result);

$result['time_generated'] = time();
file_put_contents(__DIR__ . '/datasets/taipei.json', json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
