                        <div id="status" class="settings-tab-content">
                            <div class="settings-card panel">
                                <h3>Shop Status</h3>
                                <form action="{{ route('seller.settings.status') }}" method="POST" data-enable-loading>
                                    @csrf
                                    @method('PATCH')

                                    <div class="radio-group"><label><input type="radio" name="shop_status" value="open" {{ $currentShopStatus === 'open' ? 'checked' : '' }}> Open</label></div>
                                    <div class="radio-group"><label><input type="radio" name="shop_status" value="temporarily_closed" {{ $currentShopStatus === 'temporarily_closed' ? 'checked' : '' }}> Temporarily Closed</label></div>
                                    <div class="form-group settings-inline-date" data-status-until-group>
                                        <label for="shop_status_until">Until</label>
                                        <input type="date" id="shop_status_until" name="shop_status_until" value="{{ $currentShopStatusUntil }}">
                                    </div>
                                    <div class="radio-group"><label><input type="radio" name="shop_status" value="vacation" {{ $currentShopStatus === 'vacation' ? 'checked' : '' }}> Vacation Mode</label></div>
                                    @error('shop_status')
                                        <small class="error-text">{{ $message }}</small>
                                    @enderror
                                    @error('shop_status_until')
                                        <small class="error-text">{{ $message }}</small>
                                    @enderror

                                    <button type="submit" class="page-action-btn" data-enable-loading
                                        data-loading-text="Saving...">Save</button>
                                </form>
                            </div>
                        </div>
