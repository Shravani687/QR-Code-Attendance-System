<?php
$conn = new mysqli("localhost", "root", "", "attendance_db");
require('vendor\setasign\fpdf\fpdf.php');

if (isset($_GET['format']) && $_GET['format'] == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="attendance_report.csv"');

    $output = fopen("php://output", "w");
    fputcsv($output, ["Student Name", "Roll No", "Subject", "Attendance Time"]);

    $query = "SELECT students.name, students.roll_no, subjects.subject_name, attendance.attendance_time 
              FROM attendance 
              JOIN students ON attendance.student_id = students.id
              JOIN subjects ON attendance.subject_id = subjects.id";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit();
}

if (isset($_GET['format']) && $_GET['format'] == 'pdf') {
    class PDF extends FPDF {
        function Header() {
            $this->SetFont('Arial', 'B', 14);
            $this->Cell(190, 10, 'Attendance Report', 1, 1, 'C');
            $this->Ln(5);
        }
        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 10);
            $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);

    $pdf->Cell(50, 10, "Student Name", 1);
    $pdf->Cell(30, 10, "Roll No", 1);
    $pdf->Cell(50, 10, "Subject", 1);
    $pdf->Cell(50, 10, "Attendance Time", 1);
    $pdf->Ln();

    $query = "SELECT students.name, students.roll_no, subjects.subject_name, attendance.attendance_time 
              FROM attendance 
              JOIN students ON attendance.student_id = students.id
              JOIN subjects ON attendance.subject_id = subjects.id";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(50, 10, $row['name'], 1);
        $pdf->Cell(30, 10, $row['roll_no'], 1);
        $pdf->Cell(50, 10, $row['subject_name'], 1);
        $pdf->Cell(50, 10, $row['attendance_time'], 1);
        $pdf->Ln();
    }

    $pdf->Output("D", "attendance_report.pdf");
    exit();
}

session_start();
$conn = new mysqli("localhost", "root", "", "attendance_db");
require('vendor/setasign/fpdf/fpdf.php');

if (!isset($_SESSION["student_id"])) {
    die("Unauthorized access.");
}

$student_id = $_SESSION["student_id"];

if (isset($_GET['format']) && $_GET['format'] == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="attendance_report.csv"');

    $output = fopen("php://output", "w");
    fputcsv($output, ["Subject", "Attendance Time"]);

    $query = "SELECT subjects.subject_name, attendance.attendance_time 
              FROM attendance 
              JOIN subjects ON attendance.subject_id = subjects.id
              WHERE attendance.student_id = $student_id";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit();
}

if (isset($_GET['format']) && $_GET['format'] == 'pdf') {
    class PDF extends FPDF {
        function Header() {
            $this->SetFont('Arial', 'B', 14);
            $this->Cell(190, 10, 'Attendance Report', 1, 1, 'C');
            $this->Ln(5);
        }
        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 10);
            $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);

    $pdf->Cell(95, 10, "Subject", 1);
    $pdf->Cell(95, 10, "Attendance Time", 1);
    $pdf->Ln();

    $query = "SELECT subjects.subject_name, attendance.attendance_time 
              FROM attendance 
              JOIN subjects ON attendance.subject_id = subjects.id
              WHERE attendance.student_id = $student_id";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(95, 10, $row['subject_name'], 1);
        $pdf->Cell(95, 10, $row['attendance_time'], 1);
        $pdf->Ln();
    }

    $pdf->Output("D", "attendance_report.pdf");
    exit();
}
?>

?>
