<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rentals - Zoomwheels</title>
</head>
<body>
    <h1>Manage Rentals</h1>
    <p><strong>Welcome, <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>!</strong></p>
    <form action="/rentals/create" method="get" style="display:inline;">
        <button type="submit">Create New Rental</button>
    </form>
    <br><br>
    <table border="1" cellpadding="5" id="rentals-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Brand</th>
                <th>Model</th>
                <th>License Plate</th>
                <th>Daily Rate</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($rentals)): ?>
                <?php foreach ($rentals as $rental): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($rental['rental_id']); ?></td>
                        <td><?php echo htmlspecialchars($rental['car_brand']); ?></td>
                        <td><?php echo htmlspecialchars($rental['car_model']); ?></td>
                        <td><?php echo htmlspecialchars($rental['car_license_plate']); ?></td>
                        <td><?php echo htmlspecialchars($rental['car_daily_rate']); ?></td>
                        <td><?php echo htmlspecialchars($rental['rental_status']); ?></td>
                        <td>
                            <a href="/rentals/edit/<?php echo htmlspecialchars($rental['rental_id']); ?>">
                                <button type="button">Edit</button>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7">No rentals found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <br>
    <!-- Pagination Controls -->
    <?php if (isset($totalPages) && $totalPages > 1): ?>
    <div style="margin-bottom: 1em;">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>"><button>Previous</button></a>
        <?php else: ?>
            <button disabled>Previous</button>
        <?php endif; ?>
        <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
        <?php if ($page < $totalPages): ?>
            <a href="?page=<?php echo $page + 1; ?>"><button>Next</button></a>
        <?php else: ?>
            <button disabled>Next</button>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <form action="/dashboard" method="get" style="display:inline;">
        <button type="submit">Back to Dashboard</button>
    </form>
</body>
</html>
