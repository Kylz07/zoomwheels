<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Rental - Zoomwheels</title>
</head>
<body>
    <h1>Rental Record to be Deleted</h1>
    <?php if (!empty($error)): ?>
        <p style="color:red;"><strong><?php echo htmlspecialchars($error); ?></strong></p>
    <?php endif; ?>
    <?php if (!empty($rental) && empty($error)): ?>
        <p>Are you sure you want to delete the following rental record?</p>
        <form action="/rentals/delete/<?php echo htmlspecialchars($rental['rental_id']); ?>" method="post">
            <label for="car_brand">Car Brand:</label>
            <input type="text" id="car_brand" name="car_brand" value="<?php echo htmlspecialchars($rental['car_brand']); ?>" disabled><br><br>
            <label for="car_model">Car Model:</label>
            <input type="text" id="car_model" name="car_model" value="<?php echo htmlspecialchars($rental['car_model']); ?>" disabled><br><br>
            <label for="car_license_plate">License Plate:</label>
            <input type="text" id="car_license_plate" name="car_license_plate" value="<?php echo htmlspecialchars($rental['car_license_plate']); ?>" disabled><br><br>
            <label for="car_daily_rate">Daily Rate:</label>
            <input type="number" step="0.01" id="car_daily_rate" name="car_daily_rate" value="<?php echo htmlspecialchars($rental['car_daily_rate']); ?>" disabled><br><br>
            <label for="rental_status">Status:</label>
            <input type="text" id="rental_status" name="rental_status" value="<?php echo htmlspecialchars($rental['rental_status']); ?>" disabled><br><br>
            <button type="submit" style="color:white;background-color:red;">Confirm Delete</button>
            <a href="/rentals" style="margin-left:10px;"><button type="button">Cancel</button></a>
        </form>
    <?php elseif (!empty($error)): ?>
        <p>Unable to display rental details for deletion.</p>
    <?php else: ?>
        <p>Rental not found.</p>
    <?php endif; ?>
</body>
</html>
