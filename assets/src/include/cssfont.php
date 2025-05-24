

<?php
function customer($name) {
    return '<span class="special-elite-regular">' . htmlspecialchars($name) . '</span>';
    return '<span class="special-elite-regular">' . htmlspecialchars($name) . '</span>';
}

function customerPhone($phone) {
    return '<span class="rowdies-regular ">' . htmlspecialchars($phone) . '</span>';
}

function customerAddress($address) {
    return '<span class="customer-address">' . htmlspecialchars($address) . '</span>';
}
?>