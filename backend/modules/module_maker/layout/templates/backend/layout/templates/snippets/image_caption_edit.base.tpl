							<div class="box">
								<div class="heading">
									<h3>
										<label for="{$lower_ccased_label}">{$lbl{$camel_cased_label}|ucfirst}{$required_html}</label>
									</h3>
								</div>
								<div class="options">
									<p>
										{option:item.{$underscored_label}}
											<img src="{$FRONTEND_FILES_URL}/{$module}/{$underscored_label}/{$image_size}/{$item.{$underscored_label}}"/>
										{/option:item.{$underscored_label}}
										{$file{$camel_cased_label}} {$file{$camel_cased_label}Error}
									</p>
									<p>
										<label for="{$lower_ccased_label}Caption">{$lbl{$camel_cased_label}Caption|ucfirst}</label>
										{$txt{$camel_cased_label}Caption} {$txt{$camel_cased_label}CaptionError}
									</p>
								</div>
							</div>

