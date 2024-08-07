<?php

// Empty description page
function display_pool_status_description() {
    ?>
    <div class="wrap">
        <h1><?php _e('Pool Status Widget Description', 'pool-status-widget'); ?></h1>
        <p><?php _e('This page will contain the description and information about the Pool Status Widget plugin.', 'pool-status-widget'); ?></p>
        <!-- Add more content here as needed -->
    </div>
    <?php
}

// Add admin menu
function pool_status_admin_menu() {
    add_menu_page(
        __('Pool Status', 'pool-status-widget'),
        __('Pool Status', 'pool-status-widget'),
        'manage_options',
        'pool-status',
        'display_pool_status_description',
        'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB2ZXJzaW9uPSIxLjEiIHdpZHRoPSIyNTYiIGhlaWdodD0iMjU2IiB2aWV3Qm94PSIwIDAgMjU2IDI1NiIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+Cgo8ZGVmcz4KPC9kZWZzPgo8ZyBzdHlsZT0ic3Ryb2tlOiBub25lOyBzdHJva2Utd2lkdGg6IDA7IHN0cm9rZS1kYXNoYXJyYXk6IG5vbmU7IHN0cm9rZS1saW5lY2FwOiBidXR0OyBzdHJva2UtbGluZWpvaW46IG1pdGVyOyBzdHJva2UtbWl0ZXJsaW1pdDogMTA7IGZpbGw6IG5vbmU7IGZpbGwtcnVsZTogbm9uemVybzsgb3BhY2l0eTogMTsiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDEuNDA2NTkzNDA2NTkzNDAxNiAxLjQwNjU5MzQwNjU5MzQwMTYpIHNjYWxlKDIuODEgMi44MSkiID4KCTxwYXRoIGQ9Ik0gNjIuNCA2Ny45MzggYyAtMy4yNzUgMCAtNi41NDggLTEuNTA4IC05LjczMSAtNC41MjQgYyAtNS4yMzIgLTQuOTU1IC0xMC4xMDUgLTQuOTU3IC0xNS4zMzcgMCBjIC02LjM2NyA2LjAzMiAtMTMuMDk2IDYuMDMyIC0xOS40NjMgMCBjIC01LjIzMSAtNC45NTUgLTEwLjEwNCAtNC45NTUgLTE1LjMzNiAwIGMgLTAuNjAzIDAuNTY5IC0xLjU1MiAwLjU0NCAtMi4xMjEgLTAuMDU4IGMgLTAuNTcgLTAuNjAyIC0wLjU0NCAtMS41NTEgMC4wNTcgLTIuMTIgYyA2LjM2NyAtNi4wMyAxMy4wOTYgLTYuMDMxIDE5LjQ2MyAwIGMgNS4yMzEgNC45NTcgMTAuMTA1IDQuOTU3IDE1LjMzNiAwIGMgNi4zNjggLTYuMDMxIDEzLjA5OSAtNi4wMzEgMTkuNDYzIDAgYyA1LjIzMSA0Ljk1NiAxMC4xMDQgNC45NTggMTUuMzM3IDAgYyA2LjM2OCAtNi4wMzIgMTMuMDk5IC02LjAzIDE5LjQ2MyAwIGMgMC42MDIgMC41NjkgMC42MjcgMS41MTkgMC4wNTggMi4xMiBzIC0xLjUxOSAwLjYyNyAtMi4xMiAwLjA1OCBjIC01LjIzIC00Ljk1NSAtMTAuMTA0IC00Ljk1NSAtMTUuMzM4IDAgQyA2OC45NDggNjYuNDI5IDY1LjY3NCA2Ny45MzcgNjIuNCA2Ny45MzggeiIgc3R5bGU9InN0cm9rZTogbm9uZTsgc3Ryb2tlLXdpZHRoOiAxOyBzdHJva2UtZGFzaGFycmF5OiBub25lOyBzdHJva2UtbGluZWNhcDogYnV0dDsgc3Ryb2tlLWxpbmVqb2luOiBtaXRlcjsgc3Ryb2tlLW1pdGVybGltaXQ6IDEwOyBmaWxsOiByZ2IoMCwwLDApOyBmaWxsLXJ1bGU6IG5vbnplcm87IG9wYWNpdHk6IDE7IiB0cmFuc2Zvcm09IiBtYXRyaXgoMSAwIDAgMSAwIDApICIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiAvPgoJPHBhdGggZD0iTSA2Mi40IDc3LjgyNCBjIC0zLjI3NSAwIC02LjU0OCAtMS41MDggLTkuNzMxIC00LjUyNCBjIC01LjIzMiAtNC45NTcgLTEwLjEwNSAtNC45NTcgLTE1LjMzNyAwIGMgLTYuMzY3IDYuMDMyIC0xMy4wOTcgNi4wMyAtMTkuNDYzIDAgYyAtNS4yMzEgLTQuOTU2IC0xMC4xMDQgLTQuOTU4IC0xNS4zMzYgMCBjIC0wLjYwMyAwLjU2OSAtMS41NTIgMC41NDMgLTIuMTIxIC0wLjA1OCBjIC0wLjU3IC0wLjYwMSAtMC41NDQgLTEuNTUxIDAuMDU3IC0yLjEyIGMgNi4zNjcgLTYuMDMyIDEzLjA5OCAtNi4wMyAxOS40NjMgMCBjIDUuMjMxIDQuOTU3IDEwLjEwNSA0Ljk1NyAxNS4zMzYgMCBjIDYuMzY3IC02LjAzMiAxMy4wOTcgLTYuMDMyIDE5LjQ2MyAwIGMgNS4yMzMgNC45NTggMTAuMTA1IDQuOTU2IDE1LjMzNyAwIGMgNi4zNjcgLTYuMDMzIDEzLjEgLTYuMDMxIDE5LjQ2MyAwIGMgMC42MDIgMC41NjkgMC42MjcgMS41MiAwLjA1OCAyLjEyIGMgLTAuNTY5IDAuNjA0IC0xLjUyIDAuNjI3IC0yLjEyIDAuMDU4IGMgLTUuMjMyIC00Ljk1NSAtMTAuMTA0IC00Ljk1NyAtMTUuMzM4IDAgQyA2OC45NDggNzYuMzE1IDY1LjY3NCA3Ny44MjMgNjIuNCA3Ny44MjQgeiIgc3R5bGU9InN0cm9rZTogbm9uZTsgc3Ryb2tlLXdpZHRoOiAxOyBzdHJva2UtZGFzaGFycmF5OiBub25lOyBzdHJva2UtbGluZWNhcDogYnV0dDsgc3Ryb2tlLWxpbmVqb2luOiBtaXRlcjsgc3Ryb2tlLW1pdGVybGltaXQ6IDEwOyBmaWxsOiByZ2IoMCwwLDApOyBmaWxsLXJ1bGU6IG5vbnplcm87IG9wYWNpdHk6IDE7IiB0cmFuc2Zvcm09IiBtYXRyaXgoMSAwIDAgMSAwIDApICIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiAvPgoJPHBhdGggZD0iTSA4OS41MzEgNTEuMzUgYyAtNi4zNjMgLTYuMDMxIC0xMy4wOTYgLTYuMDMzIC0xOS40NjMgMCBjIC0xLjU2OSAxLjQ4NyAtMy4xMDYgMi41MTggLTQuNjMgMy4xMTMgbCAtMTkuOTk2IC0zOCBjIC0xLjM3NyAtMi42NDkgLTQuMDg3IC00LjI5NSAtNy4wNzMgLTQuMjk1IEggMTEuMTA3IGMgLTMuNzY1IDAgLTYuODI3IDMuMDYzIC02LjgyNyA2LjgyNyB2IDMuMTE2IGMgMCAzLjc2NSAzLjA2MyA2LjgyNyA2LjgyNyA2LjgyNyBoIDE4LjM1MiBjIDEuMTU2IDAgMi4yMDYgMC42MzYgMi43NDEgMS42NiBsIDMuMDU0IDUuODQ2IGMgMC4wMDEgMC4wMDEgMC4wMDEgMC4wMDMgMC4wMDIgMC4wMDQgbCAxLjY3MyAzLjIwMSBMIDE4LjE1NSA0OS44NDggYyAtNS44MzMgLTQuNDYxIC0xMS45MDkgLTMuOTcyIC0xNy42ODcgMS41MDEgYyAtMC42MDEgMC41NjkgLTAuNjI3IDEuNTIgLTAuMDU3IDIuMTIgYyAwLjU2OSAwLjYwMiAxLjUxOCAwLjYyNyAyLjEyMSAwLjA1OCBjIDQuNzkxIC00LjU0IDkuMjgyIC00LjkwOCAxNC4wMjEgLTEuMTM1IGMgMC4wMDUgMC4wMDkgMC4wMDcgMC4wMTggMC4wMTEgMC4wMjcgYyAwLjE5MSAwLjM1MiAwLjUwMyAwLjU5MyAwLjg1NiAwLjcwOCBjIDAuMTQ5IDAuMTM0IDAuMjk4IDAuMjU4IDAuNDQ4IDAuNCBjIDYuMzY3IDYuMDMxIDEzLjA5NyA2LjAzMSAxOS40NjMgMCBjIDUuMjMxIC00Ljk1NyAxMC4xMDUgLTQuOTU3IDE1LjMzNyAwIGMgMy4xODMgMy4wMTYgNi40NTcgNC41MjMgOS43MzEgNC41MjMgYyAwLjg1MyAwIDEuNzA3IC0wLjEwOCAyLjU1OCAtMC4zMTMgYyAwLjA4NyAtMC4wMTYgMC4xNzIgLTAuMDQyIDAuMjU3IC0wLjA3NCBjIDIuMzI5IC0wLjYxNyA0LjY0NiAtMS45ODYgNi45MTUgLTQuMTM2IGMgNS4yMzQgLTQuOTU3IDEwLjEwNSAtNC45NTUgMTUuMzM4IDAgYyAwLjYwMSAwLjU2OSAxLjU1MSAwLjU0NSAyLjEyIC0wLjA1OCBDIDkwLjE1OCA1Mi44NjkgOTAuMTMzIDUxLjkxOSA4OS41MzEgNTEuMzUgeiBNIDM1LjI2OCA1MS4zNSBjIC01LjAxIDQuNzQ3IC05LjY5MyA0LjkzNCAtMTQuNjc1IDAuNTg5IGwgMTcuNzI1IC05LjYzMSBsIDAuODgzIDEuNjkgYyAwLjI2OCAwLjUxMyAwLjc5IDAuODA2IDEuMzMxIDAuODA2IGMgMC4yMzQgMCAwLjQ3MSAtMC4wNTUgMC42OTMgLTAuMTcxIGMgMC43MzQgLTAuMzg0IDEuMDE4IC0xLjI5IDAuNjM1IC0yLjAyNCBsIC0xLjcwNCAtMy4yNjIgbCAtNS4yOTYgLTEwLjEzOCBjIC0xLjA1NCAtMi4wMTcgLTMuMTI0IC0zLjI3MSAtNS40IC0zLjI3MSBIIDExLjEwNyBjIC0yLjExIDAgLTMuODI3IC0xLjcxNyAtMy44MjcgLTMuODI3IHYgLTMuMTE2IGMgMCAtMi4xMSAxLjcxNyAtMy44MjcgMy44MjcgLTMuODI3IGggMjcuMjYxIGMgMS44NjIgMCAzLjU1MiAxLjAyNiA0LjQxNSAyLjY4NiBsIDE5LjU4MiAzNy4yMTEgYyAtMi41MTQgLTAuMDExIC01LjAyOSAtMS4yNDggLTcuNjM0IC0zLjcxNSBDIDQ4LjM2NSA0NS4zMTcgNDEuNjM1IDQ1LjMxNyAzNS4yNjggNTEuMzUgeiIgc3R5bGU9InN0cm9rZTogbm9uZTsgc3Ryb2tlLXdpZHRoOiAxOyBzdHJva2UtZGFzaGFycmF5OiBub25lOyBzdHJva2UtbGluZWNhcDogYnV0dDsgc3Ryb2tlLWxpbmVqb2luOiBtaXRlcjsgc3Ryb2tlLW1pdGVybGltaXQ6IDEwOyBmaWxsOiByZ2IoMCwwLDApOyBmaWxsLXJ1bGU6IG5vbnplcm87IG9wYWNpdHk6IDE7IiB0cmFuc2Zvcm09IiBtYXRyaXgoMSAwIDAgMSAwIDApICIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiAvPgoJPHBhdGggZD0iTSA3MS40MzYgNDQuODA0IGMgLTcuMzIzIDAgLTEzLjI4MSAtNS45NTggLTEzLjI4MSAtMTMuMjgxIHMgNS45NTggLTEzLjI4MSAxMy4yODEgLTEzLjI4MSBzIDEzLjI4MSA1Ljk1OCAxMy4yODEgMTMuMjgxIFMgNzguNzU5IDQ0LjgwNCA3MS40MzYgNDQuODA0IHogTSA3MS40MzYgMjEuMjQxIGMgLTUuNjY5IDAgLTEwLjI4MSA0LjYxMiAtMTAuMjgxIDEwLjI4MSBzIDQuNjEyIDEwLjI4MSAxMC4yODEgMTAuMjgxIHMgMTAuMjgxIC00LjYxMiAxMC4yODEgLTEwLjI4MSBTIDc3LjEwNCAyMS4yNDEgNzEuNDM2IDIxLjI0MSB6IiBzdHlsZT0ic3Ryb2tlOiBub25lOyBzdHJva2Utd2lkdGg6IDE7IHN0cm9rZS1kYXNoYXJyYXk6IG5vbmU7IHN0cm9rZS1saW5lY2FwOiBidXR0OyBzdHJva2UtbGluZWpvaW46IG1pdGVyOyBzdHJva2UtbWl0ZXJsaW1pdDogMTA7IGZpbGw6IHJnYigwLDAsMCk7IGZpbGwtcnVsZTogbm9uemVybzsgb3BhY2l0eTogMTsiIHRyYW5zZm9ybT0iIG1hdHJpeCgxIDAgMCAxIDAgMCkgIiBzdHJva2UtbGluZWNhcD0icm91bmQiIC8+CjwvZz4KPC9zdmc+',
        6
    );

    add_submenu_page(
        'pool-status',
        __('Settings', 'pool-status-widget'),
        __('Settings', 'pool-status-widget'),
        'manage_options',
        'pool-status-settings',
        'display_pool_status_settings'
    );

    add_submenu_page(
        'pool-status',
        __('Logs', 'pool-status-widget'),
        __('Logs', 'pool-status-widget'),
        'manage_options',
        'pool-status-logs',
        'display_pool_status_logs'
    );
}
add_action('admin_menu', 'pool_status_admin_menu');
