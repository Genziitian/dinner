package com.dinepos.app.core.utils

import java.text.NumberFormat
import java.util.Locale

object CurrencyFormatter {
    private val inrFormat: NumberFormat = NumberFormat.getCurrencyInstance(Locale("en", "IN")).apply {
        maximumFractionDigits = 2
        minimumFractionDigits = 2
    }

    fun formatInr(amount: Double): String {
        return "₹${String.format(Locale.US, "%.2f", amount)}"
    }

    fun formatQuantity(qty: Double, unit: String = ""): String {
        val qtyStr = if (qty % 1.0 == 0.0) {
            qty.toInt().toString()
        } else {
            String.format(Locale.US, "%.2f", qty)
        }
        return if (unit.isNotBlank()) "$qtyStr $unit" else qtyStr
    }
}
