<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Rental - Zoomwheels</title>
</head>
<body>
    <h1>Create New Rental</h1>
    <?php if (!empty($error)): ?>
        <p style="color:red;"><strong><?php echo htmlspecialchars($error); ?></strong></p>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <p style="color:green;"><strong><?php echo htmlspecialchars($success); ?></strong></p>
    <?php endif; ?>
    <form action="/rentals/create" method="post">
        <label for="car_brand">Car Brand:</label>
        <input type="text" id="car_brand" name="car_brand" required><br><br>
        <label for="car_model">Car Model:</label>
        <input type="text" id="car_model" name="car_model" required><br><br>
        <label for="car_license_plate">License Plate:</label>
        <input type="text" id="car_license_plate" name="car_license_plate" required><br><br>
        <label for="car_daily_rate">Daily Rate:</label>
        <input type="number" step="0.01" id="car_daily_rate" name="car_daily_rate" required><br><br>
        <label for="rental_status">Status:</label>
        <select id="rental_status" name="rental_status">
            <option value="available">Available</option>
            <option value="rented">Rented</option>
            <option value="out of service">Out of Service</option>
        </select><br><br>
        <button type="submit">Create Rental</button>
    </form>
    <br>
    <form action="/rentals" method="get" style="display:inline;">
        <button type="submit">Back to Rentals Dashboard</button>
    </form>
</body>
</html>
