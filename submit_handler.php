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
            // Notify department admins
            $subject = "New Inquiry: $type from $name";
            $details = '';
            foreach ($technical_data as $key => $value) {
                $details .= '<tr><td style="padding:6px 12px;border-bottom:1px solid #E2E8F0;color:#475569;font-size:13px">' . ucfirst(str_replace('_', ' ', $key)) . '</td><td style="padding:6px 12px;border-bottom:1px solid #E2E8F0;color:#0F172A;font-size:13px;font-weight:600">' . htmlspecialchars($value) . '</td></tr>';
            }
            notify_department_admins($conn, $dept_id, 'New inquiry: ' . $type, $name . ' - ' . $message, 'admin/inquiries.php', 'contact_mail', $subject, email_template('New ' . $type . ' Inquiry', '<p>A new inquiry has been submitted from the website:</p><table style="width:100%;border-collapse:collapse;margin-top:12px"><tr><td style="padding:6px 12px;border-bottom:1px solid #E2E8F0;color:#475569;font-size:13px">Name</td><td style="padding:6px 12px;border-bottom:1px solid #E2E8F0;color:#0F172A;font-size:13px;font-weight:600">' . htmlspecialchars($name) . '</td></tr><tr><td style="padding:6px 12px;border-bottom:1px solid #E2E8F0;color:#475569;font-size:13px">Email</td><td style="padding:6px 12px;border-bottom:1px solid #E2E8F0;color:#0F172A;font-size:13px;font-weight:600">' . htmlspecialchars($email) . '</td></tr><tr><td style="padding:6px 12px;border-bottom:1px solid #E2E8F0;color:#475569;font-size:13px">Message</td><td style="padding:6px 12px;border-bottom:1px solid #E2E8F0;color:#0F172A;font-size:13px;font-weight:600">' . nl2br(htmlspecialchars($message)) . '</td></tr>' . $details . '</table>'));

            echo "<script>alert('Thank you! Your request has been submitted successfully.'); window.location.href='index.html';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    $conn->close();
}
?>
