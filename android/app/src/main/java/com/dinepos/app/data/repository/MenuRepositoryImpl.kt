package com.dinepos.app.data.repository

import com.dinepos.app.core.utils.Resource
import com.dinepos.app.data.api.DinePosApiService
import com.dinepos.app.data.dto.CreateItemRequestDto
import com.dinepos.app.data.dto.CreateVariantDto
import com.dinepos.app.domain.model.ItemVariant
import com.dinepos.app.domain.model.MenuItem
import com.dinepos.app.domain.repository.MenuRepository

class MenuRepositoryImpl(private val apiService: DinePosApiService) : MenuRepository {

    override suspend fun getMenuItems(includeInactive: Boolean): Resource<Pair<List<MenuItem>, Int>> {
        return try {
            val response = apiService.getItems(if (includeInactive) "1" else null)
            if (response.isSuccessful && response.body()?.success == true) {
                val data = response.body()?.data
                val items = data?.items?.map { dto ->
                    MenuItem(
                        id = dto.id,
                        restaurantId = dto.restaurantId,
                        name = dto.name,
                        itemType = dto.itemType,
                        baseUnit = dto.baseUnit,
                        active = dto.active == 1,
                        displayPrice = dto.displayPrice,
                        variants = dto.variants.map { vDto ->
                            ItemVariant(
                                id = vDto.id,
                                itemId = vDto.itemId,
                                variantName = vDto.variantName,
                                quantityValue = vDto.quantityValue,
                                quantityUnit = vDto.quantityUnit,
                                price = vDto.price,
                                active = vDto.active == 1
                            )
                        }
                    )
                } ?: emptyList()

                val nextOrderNum = data?.previewOrderNumber ?: 1
                Resource.Success(Pair(items, nextOrderNum))
            } else {
                Resource.Error(response.body()?.message ?: "Failed to fetch menu items.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage ?: "Failed to load menu."}")
        }
    }

    override suspend fun toggleItemActive(itemId: Int): Resource<MenuItem> {
        return try {
            val response = apiService.toggleItem(itemId)
            if (response.isSuccessful && response.body()?.success == true) {
                val dto = response.body()?.data
                if (dto != null) {
                    val item = MenuItem(
                        id = dto.id,
                        restaurantId = dto.restaurantId,
                        name = dto.name,
                        itemType = dto.itemType,
                        baseUnit = dto.baseUnit,
                        active = dto.active == 1,
                        displayPrice = dto.displayPrice,
                        variants = emptyList()
                    )
                    Resource.Success(item)
                } else {
                    Resource.Error("Invalid response.")
                }
            } else {
                Resource.Error(response.body()?.message ?: "Failed to update item.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun createItem(
        name: String,
        itemType: String,
        baseUnit: String,
        active: Boolean,
        variants: List<Map<String, Any>>
    ): Resource<MenuItem> {
        return try {
            val requestDto = CreateItemRequestDto(
                name = name,
                itemType = itemType,
                baseUnit = baseUnit,
                active = if (active) 1 else 0,
                variants = variants.map { v ->
                    CreateVariantDto(
                        variantName = v["variant_name"] as? String ?: "Standard",
                        quantityValue = (v["quantity_value"] as? Number)?.toDouble() ?: 1.0,
                        quantityUnit = v["quantity_unit"] as? String ?: baseUnit,
                        price = (v["price"] as? Number)?.toDouble() ?: 0.0
                    )
                }
            )
            val response = apiService.createItem(requestDto)
            if (response.isSuccessful && response.body()?.success == true) {
                val dto = response.body()?.data
                if (dto != null) {
                    val item = MenuItem(
                        id = dto.id,
                        restaurantId = dto.restaurantId,
                        name = dto.name,
                        itemType = dto.itemType,
                        baseUnit = dto.baseUnit,
                        active = dto.active == 1,
                        displayPrice = dto.displayPrice,
                        variants = emptyList()
                    )
                    Resource.Success(item)
                } else {
                    Resource.Error("Failed to parse response.")
                }
            } else {
                Resource.Error(response.body()?.message ?: "Failed to create menu item.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }
}
