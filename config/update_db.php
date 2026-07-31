<?php
// config/update_db.php
require_once __DIR__ . '/db.php';
<<<<<<<<<<<
try {
    $db = getDB();
    
    // Create bus_stops table
    $db->exec("
        CREATE TABLE IF NOT EXISTS `bus_stops` (
            `stop_id` INT AUTO_INCREMENT PRIMARY KEY,
            `stop_name` VARCHAR(150) NOT NULL UNIQUE
        ) ENGINE=InnoDB;
    ");
    echo "Table 'bus_stops' created or already exists.\n";
    
    // Seed stops
    $stops = [
        'Ariyakulam', 'Balabagya Nagar North', 'Burkitmanagaram', 'C N Village', 
        'Chellathai Nagar', 'Chidambaram Nagar, Keelanatham', 
        'Gomathy Nagar, Balabagya Nagar North', 'Gomathy Nagar, Manimoorthispuram', 
        'K.t.c Nagar', 'Karaieruppu', 'Kayalpattinam, Thirunagar, Tirunelveli Town', 
        'Lalugapuram', 'Manappadaividu, Thoothukudi', 'Manimoorthispuram', 
        'Mehalingapuram, Selva Vignesh Nagar', 'Melakarai New Colony', 'Melakulam', 
        'Melapalayam', 'Naranammalpuram, Thoothukudi', 'Palayamkottai', 
        'Palayanchettikulam', 'Palayapettai', 'Poyalan Nagar', 'Ramnagar, Thattarmadam', 
        'Santhi Nagar', 'Selva Vignesh Nagar', 'Senthimangalam', 'Sharon Nagar', 
        'Sripuram, Thirunagar, Tirunelveli Town', 
        'Sugar Mill Colony, Sugar Mill Colony, Balabagya Nagar South, Tirunelveli Town', 
        'Thachanallur', 'Thalavaaipuram', 'Thattarmadam, Thachanallur', 
        'Thimmarajapuram', 'Thirunagar, Tirunelveli Town', 'Thiyagarajanagar', 
        'Tirunelveli', 'Tirunelveli Junction', 'Tirunelveli Town', 'Tvs Nagar', 
        'Udaya Nagar', 'V.m.chatram', 'Vanarapettai'
    ];
    
    $stmt = $db->prepare("INSERT IGNORE INTO `bus_stops` (`stop_name`) VALUES (?)");
    $inserted = 0;
    foreach ($stops as $stop) {
        $stmt->execute([$stop]);
        if ($stmt->rowCount() > 0) {
            $inserted++;
        }
    }
    echo "Seeding completed: inserted $inserted new stop(s).\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
