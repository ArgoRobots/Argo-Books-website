/**
 * Payment Portal - Client-Side Payment Flow
 *
 * Handles the checkout process for Stripe, PayPal, and Square payments
 * on the customer-facing invoice portal.
 *
 * Uses the same SDK patterns as the existing license checkout (/upgrade/standard/checkout/main.js)
 * but routes payments to the business's connected account via Stripe Connect,
 * PayPal for Marketplaces, or Square OAuth.
 */

document.addEventListener("DOMContentLoaded", function () {
  var config = window.PORTAL_CONFIG;
  if (!config) return;

  var balanceDue = config.balanceDue;
  var currency = config.currency;
  var currencySymbol = config.currencySymbol;
  var apiBase = config.apiBase;
  var invoiceToken = config.invoiceToken;

  // Payment amount handling
  var paymentType = "full";
  var paymentAmount = balanceDue;

  var fullRadio = document.querySelector('input[value="full"]');
  var partialRadio = document.querySelector('input[value="partial"]');
  var partialWrapper = document.getElementById("partial-amount-wrapper");
  var partialInput = document.getElementById("partial-amount");

  if (fullRadio) {
    fullRadio.addEventListener("change", function () {
      paymentType = "full";
      paymentAmount = balanceDue;
      if (partialWrapper) partialWrapper.style.display = "none";
    });
  }

  if (partialRadio) {
    partialRadio.addEventListener("change", function () {
      paymentType = "partial";
      if (partialWrapper) partialWrapper.style.display = "flex";
      if (partialInput) partialInput.focus();
    });
  }

  if (partialInput) {
    partialInput.addEventListener("input", function () {
      var val = parseFloat(this.value);
      if (!isNaN(val) && val > 0 && val <= balanceDue) {
        paymentAmount = val;
      }
    });
  }

  function getPaymentAmount() {
    if (paymentType === "partial" && partialInput) {
      var val = parseFloat(partialInput.value);
      if (isNaN(val) || val <= 0) {
        showError("Please enter a valid payment amount.");
        return null;
      }
      if (val > balanceDue) {
        showError("Payment amount cannot exceed the balance due.");
        return null;
      }
      return val;
    }
    return balanceDue;
  }

  // Payment method selection
  var methodButtons = document.querySelectorAll(".method-btn");
  var formContainer = document.getElementById("payment-form-container");
  var selectedMethod = null;

  methodButtons.forEach(function (btn) {
    btn.addEventListener("click", function () {
      var method = this.getAttribute("data-method");

      // Update active button
      methodButtons.forEach(function (b) {
        b.classList.remove("active");
      });
      this.classList.add("active");

      selectedMethod = method;
      if (formContainer) formContainer.style.display = "block";

      switch (method) {
        case "stripe":
          setupStripePayment();
          break;
        case "paypal":
          setupPayPalPayment();
          break;
        case "square":
          setupSquarePayment();
          break;
      }
    });
  });

  // ============= STRIPE =============
  function setupStripePayment() {
    formContainer.innerHTML =
      '<div class="loading-payment"><div class="spinner"></div><p>Loading payment form...</p></div>';

    if (typeof Stripe === "undefined") {
      var script = document.createElement("script");
      script.src = "https://js.stripe.com/v3/";
      script.onload = initializeStripeForm;
      script.onerror = function () {
        formContainer.innerHTML =
          '<div class="payment-error-box">Failed to load Stripe. Please refresh or try another payment method.</div>';
      };
      document.head.appendChild(script);
    } else {
      initializeStripeForm();
    }
  }

  function initializeStripeForm() {
    formContainer.innerHTML =
      '<form id="portal-stripe-form">' +
      '<div class="form-group">' +
      '<label for="portal-card-holder">Cardholder Name</label>' +
      '<input type="text" id="portal-card-holder" class="form-control" required>' +
      "</div>" +
      '<div class="form-group">' +
      '<label for="portal-card-element">Card Details</label>' +
      '<div id="portal-card-element" class="form-control card-element-container"></div>' +
      '<div id="portal-card-errors" class="field-error"></div>' +
      "</div>" +
      '<div class="form-group">' +
      '<label for="portal-email">Email Address <span class="optional">(for receipt)</span></label>' +
      '<input type="email" id="portal-email" class="form-control">' +
      "</div>" +
      '<button type="submit" id="portal-stripe-submit" class="btn-submit">' +
      "Pay " +
      currencySymbol +
      balanceDue.toFixed(2) +
      " " +
      currency +
      "</button>" +
      "</form>";

    // Initialize Stripe with the connected account
    var stripe = Stripe(config.stripe.publishableKey, {
      stripeAccount: config.stripe.accountId,
    });
    var elements = stripe.elements();

    var cardElement = elements.create("card", {
      style: {
        base: {
          color: "#32325d",
          fontFamily:
            '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
          fontSmoothing: "antialiased",
          fontSize: "16px",
          "::placeholder": { color: "#aab7c4" },
        },
        invalid: { color: "#fa755a", iconColor: "#fa755a" },
      },
    });
    cardElement.mount("#portal-card-element");

    cardElement.on("change", function (event) {
      var errorEl = document.getElementById("portal-card-errors");
      if (errorEl) {
        errorEl.textContent = event.error ? event.error.message : "";
      }
    });

    // Update submit button when partial amount changes
    if (partialInput) {
      partialInput.addEventListener("input", function () {
        updateSubmitButton("portal-stripe-submit");
      });
    }
    if (fullRadio) {
      fullRadio.addEventListener("change", function () {
        updateSubmitButton("portal-stripe-submit");
      });
    }

    var form = document.getElementById("portal-stripe-form");
    form.addEventListener("submit", async function (e) {
      e.preventDefault();

      var amount = getPaymentAmount();
      if (amount === null) return;

      var cardHolder = document.getElementById("portal-card-holder");
      var emailInput = document.getElementById("portal-email");
      var errorEl = document.getElementById("portal-card-errors");

      if (!cardHolder || !cardHolder.value.trim()) {
        if (errorEl) errorEl.textContent = "Please enter the cardholder name.";
        return;
      }

      showProcessing();

      try {
        // Create payment intent on server
        var response = await fetch(apiBase + "/checkout.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            invoice_token: invoiceToken,
            method: "stripe",
            amount: amount,
            email: emailInput ? emailInput.value.trim() : "",
          }),
        });

        var intentData = await response.json();
        if (!intentData.success) {
          throw new Error(intentData.message || "Failed to create payment");
        }

        // Confirm the payment
        var result = await stripe.confirmCardPayment(intentData.client_secret, {
          payment_method: {
            card: cardElement,
            billing_details: {
              name: cardHolder.value.trim(),
              email: emailInput ? emailInput.value.trim() : undefined,
            },
          },
        });

        if (result.error) {
          throw new Error(result.error.message);
        }

        // Process confirmed payment on server
        var processResponse = await fetch(apiBase + "/process-payment.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            invoice_token: invoiceToken,
            method: "stripe",
            amount: amount,
            payment_intent_id: result.paymentIntent.id,
          }),
        });

        var processData = await processResponse.json();
        if (!processData.success) {
          throw new Error(processData.message || "Failed to record payment");
        }

        showConfirmation(amount, processData.reference_number, "Credit Card");
      } catch (error) {
        hideProcessing();
        showError(error.message);
      }
    });
  }

  // ============= PAYPAL =============
  function setupPayPalPayment() {
    formContainer.innerHTML =
      '<div id="portal-paypal-container">' +
      '<div class="loading-payment"><div class="spinner"></div><p>Loading PayPal...</p></div>' +
      "</div>";

    if (typeof paypal === "undefined") {
      // First get merchant info
      fetch(apiBase + "/checkout.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          invoice_token: invoiceToken,
          method: "paypal",
          amount: balanceDue,
        }),
      })
        .then(function (r) {
          return r.json();
        })
        .then(function (data) {
          if (!data.success) throw new Error(data.message);

          var script = document.createElement("script");
          script.src =
            "https://www.paypal.com/sdk/js?client-id=" +
            config.paypal.clientId +
            "&currency=" +
            currency +
            "&merchant-id=" +
            data.merchant_id;
          script.onload = function () {
            initializePayPalButtons(data.merchant_id);
          };
          script.onerror = function () {
            document.getElementById("portal-paypal-container").innerHTML =
              '<div class="payment-error-box">Failed to load PayPal. Please refresh or try another payment method.</div>';
          };
          document.head.appendChild(script);
        })
        .catch(function (err) {
          document.getElementById("portal-paypal-container").innerHTML =
            '<div class="payment-error-box">' + err.message + "</div>";
        });
    } else {
      fetch(apiBase + "/checkout.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          invoice_token: invoiceToken,
          method: "paypal",
          amount: balanceDue,
        }),
      })
        .then(function (r) {
          return r.json();
        })
        .then(function (data) {
          if (!data.success) throw new Error(data.message);
          initializePayPalButtons(data.merchant_id);
        })
        .catch(function (err) {
          document.getElementById("portal-paypal-container").innerHTML =
            '<div class="payment-error-box">' + err.message + "</div>";
        });
    }
  }

  function initializePayPalButtons(merchantId) {
    var container = document.getElementById("portal-paypal-container");
    container.innerHTML = "";

    paypal
      .Buttons({
        createOrder: function (data, actions) {
          var amount = getPaymentAmount();
          if (amount === null) return;

          return actions.order.create({
            purchase_units: [
              {
                amount: {
                  value: amount.toFixed(2),
                  currency_code: currency,
                },
                payee: {
                  merchant_id: merchantId,
                },
                description: "Invoice " + config.invoiceId,
              },
            ],
          });
        },
        onApprove: function (data, actions) {
          showProcessing();

          return actions.order.capture().then(function (details) {
            var amount = getPaymentAmount() || balanceDue;

            return fetch(apiBase + "/process-payment.php", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({
                invoice_token: invoiceToken,
                method: "paypal",
                amount: amount,
                order_id: data.orderID,
              }),
            })
              .then(function (r) {
                return r.json();
              })
              .then(function (result) {
                if (result.success) {
                  showConfirmation(
                    amount,
                    result.reference_number,
                    "PayPal"
                  );
                } else {
                  throw new Error(result.message || "Payment processing failed");
                }
              });
          });
        },
        onError: function (err) {
          hideProcessing();
          showError(
            "There was an error processing your PayPal payment. Please try again."
          );
        },
      })
      .render("#portal-paypal-container");
  }

  // ============= SQUARE =============
  function setupSquarePayment() {
    formContainer.innerHTML =
      '<form id="portal-square-form">' +
      '<div class="form-group">' +
      '<label for="portal-sq-card-holder">Cardholder Name</label>' +
      '<input type="text" id="portal-sq-card-holder" class="form-control" required>' +
      "</div>" +
      '<div class="form-group">' +
      '<label>Card Details</label>' +
      '<div id="portal-sq-card-container" class="form-control card-element-container">' +
      '<div class="loading-payment"><div class="spinner"></div><p>Loading...</p></div>' +
      "</div>" +
      '<div id="portal-sq-errors" class="field-error"></div>' +
      "</div>" +
      '<div class="form-group">' +
      '<label for="portal-sq-email">Email Address <span class="optional">(for receipt)</span></label>' +
      '<input type="email" id="portal-sq-email" class="form-control">' +
      "</div>" +
      '<button type="submit" id="portal-square-submit" class="btn-submit">' +
      "Pay " +
      currencySymbol +
      balanceDue.toFixed(2) +
      " " +
      currency +
      "</button>" +
      "</form>";

    // First get Square config
    fetch(apiBase + "/checkout.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        invoice_token: invoiceToken,
        method: "square",
        amount: balanceDue,
      }),
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (data) {
        if (!data.success) throw new Error(data.message);
        loadSquareSDK(data.app_id, data.location_id);
      })
      .catch(function (err) {
        formContainer.innerHTML =
          '<div class="payment-error-box">' + err.message + "</div>";
      });
  }

  function loadSquareSDK(appId, locationId) {
    function initSquare() {
      var payments = window.Square.payments(appId, locationId);

      payments
        .card()
        .then(function (card) {
          var container = document.getElementById(
            "portal-sq-card-container"
          );
          if (container) container.innerHTML = "";
          card.attach("#portal-sq-card-container");

          // Update submit button when partial amount changes
          if (partialInput) {
            partialInput.addEventListener("input", function () {
              updateSubmitButton("portal-square-submit");
            });
          }
          if (fullRadio) {
            fullRadio.addEventListener("change", function () {
              updateSubmitButton("portal-square-submit");
            });
          }

          var form = document.getElementById("portal-square-form");
          form.addEventListener("submit", async function (e) {
            e.preventDefault();

            var amount = getPaymentAmount();
            if (amount === null) return;

            var cardHolder = document.getElementById(
              "portal-sq-card-holder"
            );
            var emailInput = document.getElementById("portal-sq-email");
            var errorEl = document.getElementById("portal-sq-errors");

            if (!cardHolder || !cardHolder.value.trim()) {
              if (errorEl)
                errorEl.textContent = "Please enter the cardholder name.";
              return;
            }

            showProcessing();

            try {
              var tokenResult = await card.tokenize();
              if (tokenResult.status !== "OK") {
                throw new Error(
                  tokenResult.errors[0]?.message || "Card tokenization failed"
                );
              }

              var idempotencyKey =
                Date.now().toString() +
                Math.random().toString(36).substring(2);

              var response = await fetch(apiBase + "/checkout.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                  invoice_token: invoiceToken,
                  method: "square",
                  amount: amount,
                  source_id: tokenResult.token,
                  idempotency_key: idempotencyKey,
                  email: emailInput ? emailInput.value.trim() : "",
                }),
              });

              var result = await response.json();
              if (result.success) {
                showConfirmation(
                  amount,
                  result.reference_number,
                  "Credit Card"
                );
              } else {
                throw new Error(result.message || "Payment failed");
              }
            } catch (error) {
              hideProcessing();
              showError(error.message);
            }
          });
        })
        .catch(function (err) {
          var container = document.getElementById(
            "portal-sq-card-container"
          );
          if (container) {
            container.innerHTML =
              '<div class="payment-error-box">Failed to load card form: ' +
              err.message +
              "</div>";
          }
        });
    }

    if (window.Square) {
      initSquare();
      return;
    }

    var isSandbox = appId.startsWith("sandbox-");
    var script = document.createElement("script");
    script.src = isSandbox
      ? "https://sandbox.web.squarecdn.com/v1/square.js"
      : "https://web.squarecdn.com/v1/square.js";
    script.onload = initSquare;
    script.onerror = function () {
      formContainer.innerHTML =
        '<div class="payment-error-box">Failed to load Square. Please refresh or try another payment method.</div>';
    };
    document.head.appendChild(script);
  }

  // ============= UI HELPERS =============

  function updateSubmitButton(buttonId) {
    var btn = document.getElementById(buttonId);
    if (!btn) return;

    var amount;
    if (paymentType === "partial" && partialInput) {
      amount = parseFloat(partialInput.value);
      if (isNaN(amount) || amount <= 0) amount = balanceDue;
    } else {
      amount = balanceDue;
    }
    btn.textContent = "Pay " + currencySymbol + amount.toFixed(2) + " " + currency;
  }

  function showProcessing() {
    var overlay = document.createElement("div");
    overlay.className = "processing-overlay";
    overlay.id = "portal-processing";
    overlay.innerHTML =
      '<div class="spinner"></div>' +
      "<h2>Processing Your Payment</h2>" +
      "<p>Please do not close this window or refresh the page.</p>";
    document.body.appendChild(overlay);
  }

  function hideProcessing() {
    var overlay = document.getElementById("portal-processing");
    if (overlay) overlay.remove();
  }

  function showError(message) {
    // Find or create error container
    var container = document.querySelector(".field-error:last-of-type");
    if (!container) {
      container = document.createElement("div");
      container.className = "field-error";
      if (formContainer) formContainer.appendChild(container);
    }
    container.innerHTML =
      '<div class="payment-error-box">' +
      "<strong>Error:</strong> " +
      escapeHtml(message) +
      "<p>Please try again or contact support if the problem persists.</p>" +
      "</div>";
  }

  function showConfirmation(amount, referenceNumber, method) {
    hideProcessing();

    // Hide payment section
    var paymentSection = document.getElementById("payment-section");
    if (paymentSection) paymentSection.style.display = "none";

    // Show confirmation
    var confirmation = document.getElementById("payment-confirmation");
    if (confirmation) {
      confirmation.style.display = "block";

      var confAmount = document.getElementById("conf-amount");
      var confRef = document.getElementById("conf-reference");
      var confMethod = document.getElementById("conf-method");
      var confDate = document.getElementById("conf-date");

      if (confAmount)
        confAmount.textContent =
          currencySymbol + amount.toFixed(2) + " " + currency;
      if (confRef) confRef.textContent = referenceNumber;
      if (confMethod) confMethod.textContent = method;
      if (confDate)
        confDate.textContent = new Date().toLocaleDateString("en-US", {
          year: "numeric",
          month: "long",
          day: "numeric",
        });

      // Update balance display
      var balanceEl = document.querySelector(".total-row-balance span:last-child");
      var newBalance = Math.max(0, balanceDue - amount);
      if (balanceEl) {
        balanceEl.textContent =
          currencySymbol + newBalance.toFixed(2) + " " + currency;
      }

      // Update status badge
      if (newBalance <= 0) {
        var badges = document.querySelectorAll(".status-badge");
        badges.forEach(function (badge) {
          badge.className = "status-badge status-paid";
          badge.textContent = "Paid";
        });
      }

      // Smooth scroll to confirmation
      confirmation.scrollIntoView({ behavior: "smooth", block: "center" });
    }
  }

  function escapeHtml(str) {
    var div = document.createElement("div");
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
  }
});
