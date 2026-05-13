<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = get_db_connection();
    
    $name = $conn->real_escape_string($_POST['name'] ?? 'Anonymous');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $type = $conn->real_escape_string($_POST['form_type'] ?? 'General');
    $message = $conn->real_escape_string($_POST['message'] ?? '');
    
    // Collect all other fields as JSON technical data
    $technical_data = [];
    foreach ($_POST as $key => $value) {
        if (!in_array($key, ['name', 'email', 'form_type', 'message'])) {
            $technical_data[$key] = $value;
        }
    }
    $technical_json = $conn->real_escape_string(json_encode($technical_data));
    
    // Auto-Routing Logic
    $dept_id = get_auto_assigned_department($conn, 'inquiry', $type . ' ' . $message);

    $sql = "INSERT INTO inquiries (name, email, type, message, technical_data, department_id) 
            VALUES ('$name', '$email', '$type', '$message', '$technical_json', " . ($dept_id ?: "NULL") . ")";
    
    if ($conn->query($sql) === TRUE) {
        // Prepare Email
        $to = get_setting('smtp_from_email', 'admin@wilsolvewel.com');
        $subject = "New Inquiry: $type from $name";
        $email_body = "New $type inquiry received.\n\n";
        $email_body .= "Name: $name\n";
        $email_body .= "Email: $email\n";
        $email_body .= "Message: $message\n\n";
        $email_body .= "Technical Details:\n";
        foreach ($technical_data as $key => $value) {
            $email_body .= ucfirst(str_replace('_', ' ', $key)) . ": $value\n";
        }
        
        $headers = "From: " . get_setting('smtp_from_name', 'Wilsolvewel Engineering') . " <" . get_setting('smtp_from_email', 'noreply@wilsolvewel.com') . ">";
        
        // Send email (Using mail() as fallback, but logic for SMTP would go here)
        // In a real scenario with full SMTP access, you'd use PHPMailer with the settings from the DB.
        @mail($to, $subject, $email_body, $headers);
        
        echo "<script>alert('Thank you! Your request has been submitted successfully.'); window.location.href='index.html';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    $conn->close();
}
?>
