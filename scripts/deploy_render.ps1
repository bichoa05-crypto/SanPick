<#
PowerShell helper to create a Render Web Service from this GitHub repo.

USAGE (recommended safe flow):
1) Create a Render API key: https://dashboard.render.com/u/settings?add-api-key
2) Export it in your shell (PowerShell):
   $env:RENDER_API_KEY = "<your_key_here>"
3) Edit the variables below (RepoUrl, ServiceName, DB_*). Defaults point to your repo.
4) Run this script in PowerShell (not as Admin):
   pwsh ./scripts/deploy_render.ps1

SECURITY:
- Do NOT paste your API key into public chat. The key must be kept secret.
- This script will create a service and set environment variables (it will NOT create a managed DB).
- After a successful create, follow the printed instructions to import `database/datlichthethao.sql` into your MySQL instance.

NOTES:
- The Render API is evolving. If the script fails, paste the error and I'll help troubleshoot.
#>

param()

# --- Configuration (edit if needed) -------------------------------------------------
$RepoUrl = "https://github.com/bichoa05-crypto/SanPick"
$Branch = "main"
$ServiceName = "SanPick"
$Region = "oregon"          # render region (oregon, virginia, singapore, frankfurt, ohio)
$Plan = "starter"           # plan to use; change if you want paid

# Database envs (set to values for an existing DB you control, or fill with Render DB later)
$DB_HOST = Read-Host -Prompt "DB_HOST (e.g. host or IP)"
$DB_PORT = Read-Host -Prompt "DB_PORT (e.g. 3306)"
$DB_NAME = Read-Host -Prompt "DB_NAME (e.g. datlichthethao)"
$DB_USER = Read-Host -Prompt "DB_USER"
$DB_PASS = Read-Host -Prompt "DB_PASS (will be used as an env var)"

# ----------------------------------------------------------
if (-not $env:RENDER_API_KEY) {
    Write-Error "RENDER_API_KEY environment variable is required. Create it and re-run."
    exit 1
}
$ApiKey = $env:RENDER_API_KEY
$Headers = @{ Authorization = "Bearer $ApiKey"; Accept = 'application/json' }

Write-Host "Listing workspaces accessible to the API key..."
try {
    $owners = Invoke-RestMethod -Uri 'https://api.render.com/v1/owners' -Headers $Headers -Method Get
} catch {
    Write-Error "Failed to list workspaces: $_"
    exit 1
}

if ($owners.Count -eq 0) {
    Write-Error "No workspace (owner) available for this API key."
    exit 1
}

# If multiple owners, pick the first but show options
Write-Host "Found workspaces:"
for ($i=0; $i -lt $owners.Count; $i++) {
    $o = $owners[$i].owner
    Write-Host "[$i] $($o.name) (id: $($o.id)) type=$($o.type)"
}
$idx = Read-Host -Prompt "Select workspace index to create the service in (default 0)"
if ([string]::IsNullOrEmpty($idx)) { $idx = 0 }
$ownerId = $owners[$idx].owner.id

# Build service payload
$envVars = @(
    @{ key = 'DB_HOST'; value = $DB_HOST; secure = $false },
    @{ key = 'DB_PORT'; value = $DB_PORT; secure = $false },
    @{ key = 'DB_NAME'; value = $DB_NAME; secure = $false },
    @{ key = 'DB_USER'; value = $DB_USER; secure = $false },
    @{ key = 'DB_PASS'; value = $DB_PASS; secure = $true }
)

$servicePayload = @{
    type = 'web_service'
    name = $ServiceName
    ownerId = $ownerId
    repo = $RepoUrl
    branch = $Branch
    autoDeploy = 'yes'
    serviceDetails = @{
        runtime = 'docker'
        region = $Region
        plan = $Plan
        envSpecificDetails = @{
            docker = @{
                dockerfilePath = './Dockerfile'
            }
        }
    }
    envVars = $envVars
}

$body = $servicePayload | ConvertTo-Json -Depth 10

Write-Host "Creating Render Web Service..."
try {
    $resp = Invoke-RestMethod -Uri 'https://api.render.com/v1/services' -Headers @{ Authorization = "Bearer $ApiKey"; 'Content-Type' = 'application/json' } -Method Post -Body $body
} catch {
    Write-Error "Create service failed: $_"
    exit 1
}

if ($resp -and $resp.service) {
    $dashboardUrl = $resp.service.dashboardUrl
    $serviceId = $resp.service.id
    Write-Host "Service created: $dashboardUrl"
    Write-Host "Service ID: $serviceId"
    Write-Host "Render will now attempt to build/deploy from repo branch '$Branch'."
    Write-Host "If the build needs additional env/secrets, configure them in the Render Dashboard: $dashboardUrl"
    Write-Host "After the service is live, import the DB SQL as instructed below."

    Write-Host "\nImport database (run from a machine that can reach your DB host):"
    Write-Host "mysql -h $DB_HOST -P $DB_PORT -u $DB_USER -p $DB_NAME < database/datlichthethao.sql"
    Write-Host "(Replace command with preferred import method if you use a GUI or Render-managed DB.)"
} else {
    Write-Error "Unexpected response creating service: $($resp | ConvertTo-Json)"
}

Write-Host "Done. If anything failed, paste the error output here and I'll help fix it."
