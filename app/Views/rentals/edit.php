<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Rental - Zoomwheels</title>
</head>
<body>
    <h1>Edit Rental</h1>
    <?php 
    // Show error or success message (best practice: use variables, not direct $_GET in view)
    if (!empty($error)) { ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php } elseif (!empty($success)) { ?>
        <p style="color:green;"><?php echo htmlspecialchars($success); ?></p>
    <?php } ?>
    <form action="/rentals/edit/<?php echo htmlspecialchars($rental['rental_id']); ?>" method="post">
        <input type="hidden" name="rental_id" value="<?php echo htmlspecialchars($rental['rental_id']); ?>">
        <label for="car_brand">Car Brand:</label>
        <input type="text" id="car_brand" name="car_brand" value="<?php echo htmlspecialchars($rental['car_brand']); ?>" disabled><br><br>
        <label for="car_model">Car Model:</label>
        <input type="text" id="car_model" name="car_model" value="<?php echo htmlspecialchars($rental['car_model']); ?>" disabled><br><br>
        <label for="car_license_plate">License Plate:</label>
        <input type="text" id="car_license_plate" name="car_license_plate" value="<?php echo htmlspecialchars($rental['car_license_plate']); ?>" disabled><br><br>
        <label for="car_daily_rate">Daily Rate:</label>
        <input type="number" step="0.01" id="car_daily_rate" name="car_daily_rate" value="<?php echo htmlspecialchars($rental['car_daily_rate']); ?>" required><br><br>
        <label for="rental_status">Status:</label>
        <select id="rental_status" name="rental_status">
            <option value="available" <?php if ($rental['rental_status'] === 'available') echo 'selected'; ?>>Available</option>
            <option value="rented" <?php if ($rental['rental_status'] === 'rented') echo 'selected'; ?>>Rented</option>
            <option value="out of service" <?php if ($rental['rental_status'] === 'out of service') echo 'selected'; ?>>Out of Service</option>
        </select><br><br>
        <button type="submit" style="color:white;background-color:green;">Update</button>
        <a href="/rentals" style="margin-left:10px;"><button type="button" style="color:black;background-color:lightgray;">Back</button></a>
    </form>
    <br>

</body>
</html>
