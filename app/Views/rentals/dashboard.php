<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h1>Zoomwheels</h1>
    <p><strong>Welcome, <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>!</strong></p>
    <form action="/rentals/create" method="get" style="display:inline-block;">
        <button type="submit" style="color:white;background-color:green;">+ Create New</button>
    </form>
    <p style="display:inline-block;"><a href="/logout"><button type="button" style="color:white;background-color:red;">Logout</button></a></p>
    <br><br>
    <form method="get" action="/rentals" style="margin-bottom:1em;">
        <label for="filter_status">Status:</label>
        <select name="filter_status" id="filter_status" onchange="this.form.submit()">
            <option value="">All</option>
            <option value="available" <?php if (!empty($_GET['filter_status']) && $_GET['filter_status']==='available') echo 'selected'; ?>>Available</option>
            <option value="rented" <?php if (!empty($_GET['filter_status']) && $_GET['filter_status']==='rented') echo 'selected'; ?>>Rented</option>
            <option value="out of service" <?php if (!empty($_GET['filter_status']) && $_GET['filter_status']==='out of service') echo 'selected'; ?>>Out of Service</option>
        </select>
        <label for="filter_brand" style="margin-left:1em;">Brand:</label>
        <select name="filter_brand" id="filter_brand" onchange="this.form.submit()">
            <option value="">All</option>
            <?php if (!empty($brands)): ?>
                <?php foreach ($brands as $brand): ?>
                    <option value="<?php echo htmlspecialchars($brand); ?>" <?php if (!empty($_GET['filter_brand']) && $_GET['filter_brand'] === $brand) echo 'selected'; ?>><?php echo htmlspecialchars($brand); ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        
        <?php if (!empty($_GET['filter_status']) || !empty($_GET['filter_brand']) || !empty($_GET['filter_rate'])): ?>
            <a href="/rentals"><button type="button">Clear</button></a>
        <?php endif; ?>
    </form>
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
                                <button type="button" style="color:white;background-color:blue;">Edit</button>
                            </a>
                            <a href="/rentals/delete/<?php echo htmlspecialchars($rental['rental_id']); ?>">
                                <button type="button" style="color:white;background-color:red;">Delete</button>
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
    <?php
    // Build query string for filters to append to pagination links
    $filterParams = array();
    if (!empty($_GET['filter_status'])) $filterParams['filter_status'] = $_GET['filter_status'];
    if (!empty($_GET['filter_brand'])) $filterParams['filter_brand'] = $_GET['filter_brand'];
    if (!empty($_GET['filter_rate'])) $filterParams['filter_rate'] = $_GET['filter_rate'];
    $filterQuery = http_build_query($filterParams);
    ?>
    <?php if (isset($totalPages) && $totalPages > 1): ?>
    <div style="margin-bottom: 1em;">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?><?php echo $filterQuery ? '&' . $filterQuery : ''; ?>"><button>Previous</button></a>
        <?php else: ?>
            <button disabled>Previous</button>
        <?php endif; ?>
        <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
        <?php if ($page < $totalPages): ?>
            <a href="?page=<?php echo $page + 1; ?><?php echo $filterQuery ? '&' . $filterQuery : ''; ?>"><button>Next</button></a>
        <?php else: ?>
            <button disabled>Next</button>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</body>
</html>
