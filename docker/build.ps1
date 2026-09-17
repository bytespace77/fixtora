$ErrorActionPreference = "Stop"

$Image = if ($env:FIXTORA_IMAGE) { $env:FIXTORA_IMAGE } else { "localhost/fixtora:latest" }

podman build --format=docker --network=host -t $Image -f Containerfile .

Write-Host "Built $Image"
