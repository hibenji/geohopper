# Geohopper Overview

Geohopper is a revolutionary 3D Traceroute software. It's a cutting-edge tool that transforms traditional traceroute data into interactive 3D maps. It's designed to provide detailed insights into network paths in a visually engaging way.

## Supported Operating Systems

Geohopper has been tested and is supported on the following operating systems:

- Ubuntu 22.04
- Debian 11
- More... (Any Linux distro *should* work)

## Installation Guide

Installing Geohopper is straightforward. Follow these steps:

### Linux
1. Open your terminal
2. Run the install script: `curl -s https://geohopper.net/installer | bash`
3. It will install all the dependencies and you are good to go!

### Windows
A little harder atm since i have little Windows experience.
1. Open PowerShell with Administrator perms.
2. Run the install script: <br>
  ```irm https://geohopper.net/installer.ps1 | iex``` <br>
   **or** <br>
   ```iex ((New-Object System.Net.WebClient).DownloadString('https://geohopper.net/installer.ps1'))```
4. Now open your normal command line and use it with `geohopper <IP>`

## Usage

Geohopper is a command-line tool. To use it, simply run:
```geohopper <IP>```

For example:
```geohopper 1.1.1.1```

You can also use domain names:
```geohopper google.com```

## Example

Here is an example of a Geohopper map: [From Germany to Japan](https://geohopper.net/globe.php?code=6570e8714e79c)
Here is an example of a cooler Geohopper map (performance intensive): [From Germany to Japan](https://geohopper.net/cool_globe.php?code=6570e8714e79c)

![Geohopper Screenshot](geohopper_screenshot.png)

## License

© 2023 Geohopper Team. All rights reserved.

Made with ♥ by [Benji](https://benji.link)
