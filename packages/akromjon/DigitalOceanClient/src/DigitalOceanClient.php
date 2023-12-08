<?php

namespace Akromjon\DigitalOceanClient;

use Akromjon\DigitalOceanClient\Base;

class DigitalOceanClient extends Base
{
    public function projects(): array
    {
        $response = $this->baseHTTP('get', 'projects');
        return $this->wrapInArray($response->json(), 'projects');
    }

    public function project(string $projectId): array
    {
        $response = $this->baseHTTP('get', 'projects/' . $projectId);
        return $this->wrapInArray($response->json(), 'project');
    }

    public function projectResources(string $projectId): array
    {
        $response = $this->baseHTTP('get', 'projects/' . $projectId . '/resources');
        return $this->wrapInArray($response->json(), 'resources');
    }

    public function createProject(string $name, string $purpose="", string $description="",string $environment=""): array
    {
        $response = $this->baseHTTP('post', 'projects', [
            'name' => $name,
            'purpose' => ''==$purpose ? $this->getProjectPurposes()[4] : $purpose,
            'description' => ''==$description ? $this->getProjectPurposes()[4] : $description,
            'environment' => ''==$environment ? 'Development' : $environment,
        ]);

        return $this->wrapInArray($response->json(), 'project');
    }

    public function updateProject(string $projectId, string $name = "", string $purpose = "", string $description = "", string $environment = "", bool $isDefault = false): array
    {
        $project = $this->project($projectId);

        $params = [
            'name' => $name != "" ? $name : $project['name'],
            'purpose' => $purpose != "" ? $purpose : $project['purpose'],
            'description' => $description != "" ? $description : $project['description'],
            'environment' => $environment != "" ? $environment : $project['environment'],
            'is_default' => $isDefault ? $isDefault : $project['is_default'],
        ];

        $response = $this->baseHTTP('put', 'projects/' . $projectId, $params);

        return $this->wrapInArray($response->json(), 'project');
    }

    public function deleteProject(string $projectId): array
    {

        $response = $this->baseHTTP('delete', 'projects/' . $projectId, [
            'project_id' => $projectId
        ]);

        return $this->wrapInArray($response->json(), 'project');
    }

    public function defaultProject(): array
    {
        $response = $this->baseHTTP('get', 'projects/default');

        return $this->wrapInArray($response->json(), 'project');
    }

    public function sizes(): array
    {
        $response = $this->baseHTTP('get', 'sizes');

        return $this->wrapInArray($response->json(), 'sizes');
    }

    public function snapshots(string $resourceType = "droplet"): array
    {
        $response = $this->baseHTTP('get', 'snapshots', [
            'resource_type' => $resourceType
        ]);

        return $this->wrapInArray($response->json(), 'snapshots');
    }

    public function snapshot(string $snapshotId): array
    {
        $response = $this->baseHTTP('get', 'snapshots/' . $snapshotId);

        return $this->wrapInArray($response->json(), 'snapshot');
    }

    public function vpcs():array
    {
        $response = $this->baseHTTP('get', 'vpcs');

        return $this->wrapInArray($response->json(), 'vpcs');
    }






}
