<?php

$ftp_host = 'ftpupload.net';
$ftp_user = 'if0_41046441';
$ftp_pass = '8z6nQBA9JFeHDL';

$local_dir = __DIR__;
$remote_dir = '/htdocs';

// Connect and login
$conn_id = ftp_connect($ftp_host) or die("Could not connect to $ftp_host");
if (@ftp_login($conn_id, $ftp_user, $ftp_pass)) {
    echo "Connected as $ftp_user@$ftp_host\n";
} else {
    echo "Could not connect as $ftp_user\n";
    exit;
}

ftp_pasv($conn_id, true);

function upload_dir($conn_id, $local_path, $remote_path) {
    // Create remote directory if not exists
    if (!@ftp_chdir($conn_id, $remote_path)) {
        if (!@ftp_mkdir($conn_id, $remote_path)) {
            // Might exist
        }
        ftp_chdir($conn_id, $remote_path);
    }

    $files = scandir($local_path);
    $ignore = ['.', '..', '.git', '.gemini', 'node_modules', 'deploy_to_infinityfree.php', 'shop_db.sql'];

    foreach ($files as $file) {
        if (in_array($file, $ignore)) continue;

        $local_file = $local_path . '/' . $file;
        $remote_file = $remote_path . '/' . $file;

        if (is_dir($local_file)) {
            upload_dir($conn_id, $local_file, $remote_file);
        } else {
            echo "Uploading: $file... ";
            // Use FTP_BINARY for all files to ensure images don't corrupt
            if (ftp_put($conn_id, $remote_file, $local_file, FTP_BINARY)) {
                echo "Success\n";
            } else {
                echo "ERROR\n";
            }
        }
    }
}

// Perform FULL synchronization
upload_dir($conn_id, $local_dir, $remote_dir);

ftp_close($conn_id);
echo "Full Deployment Complete!\n";
