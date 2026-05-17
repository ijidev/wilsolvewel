<?php
require_once 'config.php';
secure_session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        csrf_error_response();
    }

    $conn = get_db_connection();

    $name = $_POST['name'] ?? 'Anonymous';
    $email = $_POST['email'] ?? '';
    $type = $_POST['form_type'] ?? 'General';
    $message = $_POST['message'] ?? '';

    // Collect all other fields as JSON technical data
    $technical_data = [];
    foreach ($_POST as $key => $value) {
        if (!in_array($key, ['name', 'email', 'form_type', 'message', 'csrf_token'])) {
            $technical_data[$key] = $value;
        }
    }
    $technical_json = json_encode($technical_data);

    // Auto-Routing Logic
    $dept_id = get_auto_assigned_department($conn, 'inquiry', $type . ' ' . $message);

    $stmt = $conn->prepare("INSERT INTO inquiries (name, email, type, message, technical_data, department_id) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssssi", $name, $email, $type, $message, $technical_json, $dept_id);

        if ($stmt->execute()) {
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

            @mail($to, $subject, $email_body, $headers);

            echo "<script>alert('Thank you! Your request has been submitted successfully.'); window.location.href='index.html';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    $conn->close();
}
?>
