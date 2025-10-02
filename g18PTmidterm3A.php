<?php
// index.php - Student Grade Calculator without any CSS

function h($v) { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
$subjects = ['Mathematics','Science','English','History','Art'];
$errors = [];
$grades = array_fill_keys($subjects, null);
$name = '';
$average = null;
$letter = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $name = $name === '' ? '' : preg_replace('/\s+/', ' ', $name);

    if ($name === '') {
        $errors[] = "Please enter the student name.";
    }

    foreach ($subjects as $s) {
        $key = strtolower($s);
        $raw = isset($_POST[$key]) ? trim($_POST[$key]) : '';
        if ($raw === '') {
            $errors[] = "Please enter a grade for $s.";
            $grades[$s] = null;
            continue;
        }
        if (!is_numeric($raw)) {
            $errors[] = "Grade for $s must be numeric (0 - 100).";
            $grades[$s] = null;
            continue;
        }
        $num = floatval($raw);
        if ($num < 0 || $num > 100) {
            $errors[] = "Grade for $s must be between 0 and 100.";
            $grades[$s] = null;
            continue;
        }
        $grades[$s] = round($num, 2);
    }

    if (empty($errors)) {
        $sum = array_sum($grades);
        $count = count($grades);
        $average = $count > 0 ? round($sum / $count, 2) : 0;

        if ($average >= 98) $letter = '1.0';
        elseif ($average >= 95) $letter = '1.25';
        elseif ($average >= 92) $letter = '1.5';
        elseif ($average >= 89) $letter = '1.75';
        elseif ($average >= 86) $letter = '2.0';
        elseif ($average >= 83) $letter = '2.25';
        elseif ($average >= 80) $letter = '2.5';
        elseif ($average >= 78) $letter = '2.75';
        elseif ($average >= 75) $letter = '3.0';
        elseif ($average >= 65) $letter = '5.0';
        else $letter = 'Invalid';

        $status = ($average < 65) ? 'Invalid' : (($average >= 75) ? 'Passed' : 'Failed');

    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Student Grade Calculator</title>
</head>
<body>
  <h1>Student Grade Calculator</h1>

  <form method="post" novalidate>
    <div>
      <label for="name">Student Name</label><br>
      <input id="name" name="name" type="text" value="<?php echo h($name); ?>" placeholder="Enter student name">
    </div>

    <p>Subject grades (0-100)</p>

    <?php foreach ($subjects as $s): 
        $key = strtolower($s);
        $val = isset($grades[$s]) && $grades[$s] !== null ? $grades[$s] : '';
    ?>
      <div>
        <label for="<?php echo $key; ?>"><?php echo $s; ?></label><br>
        <input id="<?php echo $key; ?>" name="<?php echo $key; ?>" type="number" step="0.01" min="0" max="100" value="<?php echo h($val); ?>" placeholder="Enter <?php echo strtolower($s); ?> grade">
      </div>
    <?php endforeach; ?>

    <div style="margin-top:8px;">
      <button type="submit">Calculate Grade</button>
    </div>

    <?php if (!empty($errors)): ?>
      <div role="status">
        <?php foreach ($errors as $err) echo '<div>' . h($err) . '</div>'; ?>
      </div>
    <?php endif; ?>
  </form>

  <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)): ?>
    <hr>
    <h1>Result</h1>
    <h4><b>Student Report Card</b></h4>
    <h2>Student Name:</h2>
    <div><?php echo h($name); ?></div>

    <hr>
    <h6><b>Individual Subject grades:</b></h6>

        <?php foreach ($grades as $sub => $g): ?>
            <div class="subject-block" style="margin-bottom:12px;">
                <div class="subject-name" style="font-weight:bold;"><?php echo h($sub); ?></div>
                <div class="subject-grade" style="margin-top:4px;"><?php echo number_format($g, 2); ?></div>
            </div>
        <?php endforeach; ?>

    <hr>
    <h6>Summary:</h6>
    <div><strong>Average</strong>: <?php echo number_format($average, 2); ?></div>
    <div><strong>Letter Grade</strong>: <?php echo h($letter); ?></div>
    <div><strong>Status</strong>: <?php echo h($status); ?></div>
  <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($errors)): ?>
    <div><strong>There were validation errors. Fix them and submit again.</strong></div>
  <?php endif; ?>

</body>
</html>
