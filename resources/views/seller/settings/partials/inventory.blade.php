                        <div id="inventory" class="settings-tab-content">
                            <div class="settings-card panel">
                                <h3>Inventory</h3>
                                <form action="{{ route('seller.settings.inventory') }}" method="POST" data-enable-loading>
                                    @csrf
                                    @method('PATCH')

                                    <div class="form-group">
                                        <label for="low_stock_threshold">Low Stock Alert At</label>
                                        <input type="number" id="low_stock_threshold" name="low_stock_threshold" min="0" value="{{ old('low_stock_threshold', $seller->low_stock_threshold ?? 5) }}">
                                        @error('low_stock_threshold')
                                            <small class="error-text">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="checkbox-group">
                                        <label><input type="checkbox" name="hide_out_of_stock" value="1" {{ old('hide_out_of_stock', $seller->hide_out_of_stock ?? 0) ? 'checked' : '' }}> Hide sold-out products from buyers</label>
                                    </div>

                                    <button type="submit" class="page-action-btn" data-enable-loading
                                        data-loading-text="Saving...">Save</button>
                                </form>
                            </div>
                        </div>
