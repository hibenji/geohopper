# Function to determine if an IP is local
function Test-LocalIP {
    param ([string]$ip)
    if ($ip.StartsWith("192.168.") -or $ip.StartsWith("10.") -or $ip.StartsWith("172.16.") -or $ip.StartsWith("172.31.")) {
        return $true
    }
    return $false
}

# Function to check if a string is a valid IP address
function Test-ValidIP {
    param ([string]$ip)
    if ($ip -match "^\d{1,3}(\.\d{1,3}){3}$") {
        $addr = $ip -split "\."
        if (($addr[0] -le 255) -and ($addr[1] -le 255) -and ($addr[2] -le 255) -and ($addr[3] -le 255)) {
            return $true
        }
    }
    return $false
}

# Function to get IP information using ipinfo.io
function Get-IPInfo {
    param ([string]$ip)
    Invoke-RestMethod -Uri "https://geohopper.net/lookup.php" -Method Post -Body $ip -ContentType 'text/plain'
}



# Function to perform traceroute and parse output
function Perform-Traceroute {
    param ([string]$targetIp)
    tracert -d -h 30 $targetIp
}

# Main function
function Invoke-GeoHopper {
    param ([string]$targetIp)

    Write-Host "GeoHopper traceroute to ${targetIp}:"
    Write-Host "-------------------------------------"

    # Variable to hold each hop in JSON format
    $hops_json = @()

    # Perform the traceroute and process each line
    $tracerouteOutput = Perform-Traceroute -targetIp $targetIp
    foreach ($line in $tracerouteOutput) {
        if ($line -match "\s*(\d+)\s+((\d+ ms)|\*)\s+((\d+ ms)|\*)\s+((\d+ ms)|\*)\s+(\d+\.\d+\.\d+\.\d+)") {
            $hop = $matches[1]
            $ip = $matches[8]


            # Get the latencies for each hop
            $latencies = @($matches[2], $matches[4], $matches[6]) | Where-Object {$_ -ne "*"}
            
            # remove the ms from each latency and convert to int
            $latencies = $latencies | ForEach-Object {$_ -replace " ms", ""} | ForEach-Object {[int]$_}

            if ($latencies.Count -eq 0) {
                $average_latency = "*"
            } else {
                # calculate the average
                $average_latency = [int]($latencies | Measure-Object -Average).Average
            }


            if (Test-LocalIP -ip $ip) {
                Write-Host "Hop ${hop}: IP $ip`nLocation: Local`nASN Name: Local`nAverage Latency: ${average_latency}ms`n---------------------"
            } else {
                $info = Get-IPInfo -ip $ip
                $location = "$($info.city), $($info.country)"
                $asn_name = $info.org
                $coordinates = $info.loc

                $hops_json += @{
                    hop = $hop
                    ip = $ip
                    location = $location
                    asn_name = $asn_name
                    average_latency = $average_latency
                    coordinates = $coordinates
                }

                Write-Host "Hop ${hop}: IP $ip`nLocation: $location`nASN Name: $asn_name`nAverage Latency: ${average_latency}ms`nCoordinates: $coordinates`n---------------------"
            }
        }
    }

    # Convert to JSON
    $hops_json_string = $hops_json | ConvertTo-Json

    # Print the json
    # Write-Host $hops_json_string

    $response = Invoke-RestMethod -Uri 'https://geohopper.net/upload.php' -Method Post -Body $hops_json_string -ContentType 'text/plain'
    Write-Host "https://geohopper.net/globe.php?code=$response"
}

# Run the main function with the provided IP
Invoke-GeoHopper -targetIp $args[0]