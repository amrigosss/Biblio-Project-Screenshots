document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById('supplierForm');

    if (!form) {
        console.error("Form with ID 'supplierForm' not found");
        return;
    }

    // Handle submit
    form.onsubmit = function (e) {
        e.preventDefault();

        const formData = new FormData();
        formData.append('id', document.getElementById('supplierId').value);
        formData.append('name', document.getElementById('supplierName').value);
        formData.append('contact', document.getElementById('supplierContact').value);
        formData.append('address', document.getElementById('supplierAddress').value);

        fetch('../api/suppliers/addSupplier.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.text())
        .then(alert)
        .then(() => location.reload());
    };

    // Make functions globally available
    window.openAddSupplierModal = function () {
        document.getElementById('modalTitle').innerText = 'Add Supplier';
        form.reset();
        document.getElementById('supplierId').value = '';
        document.getElementById('supplierModal').style.display = 'flex';
    };

    window.openEditSupplierModal = function (id, name, contact, address) {
        document.getElementById('modalTitle').innerText = 'Edit Supplier';
        document.getElementById('supplierId').value = id;
        document.getElementById('supplierName').value = name;
        document.getElementById('supplierContact').value = contact;
        document.getElementById('supplierAddress').value = address;
        document.getElementById('supplierModal').style.display = 'flex';
    };

    window.closeSupplierModal = function () {
        document.getElementById('supplierModal').style.display = 'none';
    };

    window.deleteSupplier = function (id) {
        if (confirm("Delete this supplier?")) {
            fetch('../api/suppliers/deleteSupplier.php', {
                method: 'POST',
                body: JSON.stringify({ id }),
                headers: { 'Content-Type': 'application/json' }
            })
            .then(res => res.text())
            .then(alert)
            .then(() => location.reload());
        }
    };
});
