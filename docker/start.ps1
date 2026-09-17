$ErrorActionPreference = "Stop"

if (!(Test-Path "docker/compose.env")) {
    Copy-Item "docker/compose.env.example" "docker/compose.env"
    Write-Host "Created docker/compose.env from example. Set APP_KEY before production use."
}

podman compose --env-file docker/compose.env up -d
podman compose --env-file docker/compose.env ps
