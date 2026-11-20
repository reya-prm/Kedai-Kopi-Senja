const orderForm = document.querySelector('[data-order-form]');

if (orderForm) {
    const menuItems = Array.from(orderForm.querySelectorAll('[data-order-item]'));
    const numberFormat = new Intl.NumberFormat('id-ID');
    const summaryList = orderForm.querySelector('[data-order-summary-list]');
    const emptySummary = orderForm.querySelector('[data-order-empty]');
    const orderCount = orderForm.querySelector('[data-order-count]');
    const subtotal = orderForm.querySelector('[data-order-subtotal]');

    const renderOrderSummary = () => {
        let totalQuantity = 0;
        let totalPrice = 0;
        const rows = [];

        menuItems.forEach((item) => {
            const quantityInput = item.querySelector('[data-menu-quantity]');
            const quantity = Number.parseInt(quantityInput.value, 10) || 0;
            const isSelected = quantity > 0;
            const addButton = item.querySelector('[data-add-menu]');
            const quantityControls = item.querySelector('[data-quantity-controls]');
            const price = Number.parseInt(item.dataset.menuPrice, 10) || 0;

            addButton.classList.toggle('hidden', isSelected);
            quantityControls.classList.toggle('hidden', !isSelected);
            item.classList.toggle('border-blue-300', isSelected);
            item.classList.toggle('ring-1', isSelected);
            item.classList.toggle('ring-blue-100', isSelected);

            if (!isSelected || quantityInput.disabled) {
                return;
            }

            totalQuantity += quantity;
            totalPrice += price * quantity;

            const row = document.createElement('li');
            row.className = 'flex items-start justify-between gap-3 text-sm';

            const name = document.createElement('span');
            name.className = 'font-medium text-slate-800';
            name.textContent = item.dataset.menuName;

            const details = document.createElement('span');
            details.className = 'shrink-0 text-right tabular-nums text-slate-500';
            details.textContent = `${quantity} × Rp${numberFormat.format(price)}`;

            row.append(name, details);
            rows.push(row);
        });

        const hasItems = rows.length > 0;
        summaryList.replaceChildren(...rows);
        summaryList.classList.toggle('hidden', !hasItems);
        emptySummary.classList.toggle('hidden', hasItems);
        orderCount.textContent = numberFormat.format(totalQuantity);
        subtotal.textContent = `Rp${numberFormat.format(totalPrice)}`;
    };

    orderForm.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-add-menu], button[data-quantity-change]');

        if (!button || button.disabled) {
            return;
        }

        const item = button.closest('[data-order-item]');
        const quantityInput = item.querySelector('[data-menu-quantity]');
        const currentQuantity = Number.parseInt(quantityInput.value, 10) || 0;
        const change = button.hasAttribute('data-add-menu')
            ? 1
            : Number.parseInt(button.dataset.quantityChange, 10);

        quantityInput.value = String(Math.max(0, Math.min(99, currentQuantity + change)));
        renderOrderSummary();
    });

    renderOrderSummary();
}
