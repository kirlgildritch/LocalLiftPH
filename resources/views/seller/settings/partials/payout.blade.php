                        <div id="payout" class="settings-tab-content">
                            <div class="settings-card panel">
                                <h3>Payout</h3>
                                <form action="{{ route('seller.settings.payout') }}" method="POST" data-enable-loading>
                                    @csrf
                                    @method('PATCH')

                                    <div class="form-group">
                                        <label for="payout_method">Method</label>
                                        <select id="payout_method" name="payout_method">
                                            <option value="gcash" {{ old('payout_method', $seller->payout_method ?? '') === 'gcash' ? 'selected' : '' }}>GCash</option>
                                            <option value="bank" {{ old('payout_method', $seller->payout_method ?? '') === 'bank' ? 'selected' : '' }}>Bank</option>
                                        </select>
                                        @error('payout_method')
                                            <small class="error-text">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="payout_account_name">Account Name</label>
                                        <input type="text" id="payout_account_name" name="payout_account_name" value="{{ old('payout_account_name', $seller->payout_account_name ?? '') }}">
                                        @error('payout_account_name')
                                            <small class="error-text">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="payout_account_number">Account Number</label>
                                        <input type="text" id="payout_account_number" name="payout_account_number" value="{{ old('payout_account_number', $seller->payout_account_number ?? '') }}">
                                        @error('payout_account_number')
                                            <small class="error-text">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <button type="submit" class="page-action-btn" data-enable-loading
                                        data-loading-text="Saving...">Save</button>
                                </form>
                            </div>
                        </div>
