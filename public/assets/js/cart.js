// Cart quantity
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".cart-item").forEach((item) => {
    const minus = item.querySelector(".cart-item__qty-minus");
    const plus = item.querySelector(".cart-item__qty-plus");
    const input = item.querySelector(".cart-item__qty-input");

    plus.addEventListener("click", () => {
      input.value = parseInt(input.value || 0) + 1;
    });

    minus.addEventListener("click", () => {
      let value = parseInt(input.value || 1);
      if (value > 1) input.value = value - 1;
    });

    input.addEventListener("input", () => {
      input.value = input.value.replace(/[^0-9]/g, "") || 1;
    });
  });

  // Modal
  const body = document.body;
  const NO_SCROLL_CLASS = "is-modal-open";
  document
    .querySelector(".order-summary__checkout-btn")
    .addEventListener("click", () => {
      document
        .querySelector(".c-checkout-modal")
        .classList.add("c-checkout-modal--active");
      body.classList.add(NO_SCROLL_CLASS);
    });

  document
    .querySelector(".c-checkout-modal__close")
    .addEventListener("click", () => {
      document
        .querySelector(".c-checkout-modal")
        .classList.remove("c-checkout-modal--active");
      body.classList.remove(NO_SCROLL_CLASS);
    });

  document
    .querySelector(".c-checkout-overlay")
    .addEventListener("click", () => {
      document
        .querySelector(".c-checkout-modal")
        .classList.remove("c-checkout-modal--active");
      body.classList.remove(NO_SCROLL_CLASS);
    });

  document.querySelectorAll(".cart-item__remove").forEach((btn) => {
    btn.addEventListener("click", () => {
      document
        .querySelector(".c-checkout-modal")
        .classList.add("c-checkout-modal--active");
    });
  });
});
// End cart quantity

// Viewed products
document.addEventListener("DOMContentLoaded", () => {
  // Clear all viewed products
  const clearButton = document.querySelector(".js-viewed-products__clear");

  if (clearButton) {
    clearButton.addEventListener("click", (event) => {
      event.preventDefault();
      const viewedProductsBlock = event.target.closest(".c-viewed-products");

      if (viewedProductsBlock) viewedProductsBlock.remove();
    });
  }

  // Remove individual viewed product
  const removeButtons = document.querySelectorAll(
    ".js-viewed-products__remove"
  );
  removeButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      event.preventDefault();

      const productItem = event.target.closest(".c-viewed-products__item");

      if (productItem) {
        const listContainer = productItem.parentElement;
        productItem.remove();
        if (listContainer && listContainer.children.length === 0) {
          listContainer.innerHTML = "<p>Bạn chưa xem sản phẩm nào.</p>";
        }
      }
    });
  });
});
// End viewed products - Remove item

// Related products - Size selection
document.addEventListener("DOMContentLoaded", () => {
  document.body.addEventListener("click", (event) => {
    const clickedItem = event.target.closest(".js-related-products__size-item");

    if (clickedItem) {
      const productItem = clickedItem.closest(".c-related-products__item");

      if (!productItem) return;
      const currentlyActive = productItem.querySelector(".is-active");

      if (currentlyActive) {
        currentlyActive.classList.remove("is-active");
      }

      clickedItem.classList.add("is-active");
    }
  });
});
// End related products - Size selection

// category
document.addEventListener("DOMContentLoaded", () => {
  const productGrid = document.querySelector(".product-grid");

  if (productGrid) {
    productGrid.addEventListener("click", (event) => {
      const clickedItem = event.target.closest(".js-products__size-item");

      if (!clickedItem) return;
      const productItem = clickedItem.closest(".c-products__item");

      if (!productItem) return;

      const currentlyActive = productItem.querySelector(".is-active");

      if (currentlyActive) {
        currentlyActive.classList.remove("is-active");
      }
      clickedItem.classList.add("is-active");
    });
  }
});
// End category

//

document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.querySelector(".product-listing__sidebar");

  if (sidebar) {
    sidebar.addEventListener("click", (event) => {
      const headerClicked = event.target.closest(".filter-group__top");

      if (headerClicked) {
        event.preventDefault();
        const filterGroup = headerClicked.closest(".filter-group");

        if (!filterGroup) return;
        filterGroup.classList.toggle("is-collapsed");
      }
    });
  }
});
// End viewed products - Clear all

// Sort popup
document.addEventListener("DOMContentLoaded", () => {
  const trigger = document.querySelector(".sort-trigger");
  const popup = document.querySelector(".sort-popup");
  const overlay = document.querySelector(".sort-popup__overlay");
  const closeBtn = document.querySelector(".sort-popup__close");
  const closePopup = () => popup.classList.remove("is-active");

  if (trigger && popup) {
    trigger.addEventListener("click", () => popup.classList.add("is-active"));
    overlay.addEventListener("click", () =>
      popup.classList.remove("is-active")
    );
    closeBtn.addEventListener("click", () =>
      popup.classList.remove("is-active")
    );
  }

  window.addEventListener("resize", () => {
    if (window.innerWidth > 768 && popup.classList.contains("is-active")) {
      closePopup();
    }
  });
});
// End sort popup

// Checkout modal - Select carrier
document.addEventListener("DOMContentLoaded", function () {
  const carrierItems = document.querySelectorAll(
    ".c-checkout-modal__carrier-item"
  );

  const activeClass = "c-checkout-modal__carrier-item--active";

  carrierItems.forEach((item) => {
    item.addEventListener("click", function () {
      carrierItems.forEach((i) => {
        i.classList.remove(activeClass);
      });

      this.classList.add(activeClass);
    });
  });
});
// end Checkout modal - Select carrier