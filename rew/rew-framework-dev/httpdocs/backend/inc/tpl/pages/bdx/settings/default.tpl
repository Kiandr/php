<?php if (empty($authorized)) { ?>

    <?=errorMsg('You do not have permission to access this page.', '<img src="/backend/img/ills/security.png" width=200/> Authorization Error'); ?>

<?php } else { ?>

	<form action="?submit" method="post" class="rew_check">
	
        <section>

            <header>
                <h1>BDX Settings</h1>
                <div class="app_actions">
                    <button class="btn btn--positive" type="submit">Save</button>
                </div>
            </header>
	
			<section class="col w8 p1">
				<fieldset class="grid">
				 		
				 		<div class="select_all_toggle toggle_off">Select All</div>
				 		
						<?php if (!empty($states) && is_array($states)) { ?>
							<?php foreach ($states as $state) { ?>
								<label class="state-label">
									<a class="btn edit" href="state/?id=<?=$state['value']; ?>">Edit</a>
									<input type="checkbox" class="state toggleable" name="states[<?=$state['value'];?>][enabled]" value="true"<?=(empty($settings['states']) || $settings['states'][$state['value']]['enabled'] == 'true' ? 'checked="checked"' : '');?>> <?=$state['title'];?>
								</label>
							<?php } ?>
						<?php } ?>	
				</fieldset>
			</section>
			
			<section class="col w4 p9">
		    	
		    	<div class="boxed">
				
					<fieldset class="grid">
					
						<h2>Results Limits</h2>
						
						<fieldset>
							<label>States per Page</label>
							<input type="number" max="48" min="1" value="<?=$settings['state_page_limit'];?>" name="state_page_limit">
							<p class="tip">The number of states that display per page on the state view of the application.</p>
						</fieldset>
						
						<fieldset>
							<label>Cities per Page</label>
							<input type="number" max="48" min="1" value="<?=$settings['city_page_limit'];?>" name="city_page_limit">
							<p class="tip">The number of cities that display per page on the city view of the application.</p>
						</fieldset>
						
						<fieldset>
							<label>Communities per Page</label>
							<input type="number" max="48" min="1" value="<?=$settings['community_page_limit'];?>" name="community_page_limit">
							<p class="tip">The number of communities that display per page on the search view of the application.</p>
						</fieldset>
	
						<fieldset>
							<label>Listings per Page</label>
							<input type="number" max="48" min="1" value="<?=$settings['listing_page_limit'];?>" name="listing_page_limit">
							<p class="tip">The number of listings that display per page on the community view of the application.</p>
						</fieldset>
						
						<fieldset>
							<label>Similar Listings per Page</label>
							<input type="number" max="48" min="1" value="<?=$settings['similar_listing_page_limit'];?>" name="similar_listing_page_limit">
							<p class="tip">The number of similar listings that display per page on the listings view of the application.</p>
						</fieldset>
						
					</fieldset>
					
                    <fieldset class="grid">

                        <h2>Registration Settings</h2>
                        
                        <fieldset>
                        	<label>Force Registration (Details Page)</label>
                        	<div class="buttonset radios compact">
                            	<input type="radio" name="registration_required_listing" id="registration_required_listing_true" value="true"<?=($settings['registration_required_listing'] == 'true') ? ' checked' : ''; ?>>
                                <label class="boolean" for="registration_required_listing_true">Yes</label>
                                <input type="radio" name="registration_required_listing" id="registration_required_listing_false" value="false"<?=($settings['registration_required_listing'] != 'true') ? ' checked' : ''; ?>>
                                <label class="boolean" for="registration_required_listing_false">No</label>
                          	</div>
                        </fieldset>
                        	
                        <fieldset>
                        	<label>Force Registration (Community Page)</label>
                        	<div class="buttonset radios compact">
                            	<input type="radio" name="registration_required_community" id="registration_required_community_true" value="true"<?=($settings['registration_required_community'] == 'true') ? ' checked' : ''; ?>>
                                <label class="boolean" for="registration_required_community_true">Yes</label>
                                <input type="radio" name="registration_required_community" id="registration_required_community_false" value="false"<?=($settings['registration_required_community'] != 'true') ? ' checked' : ''; ?>>
                                <label class="boolean" for="registration_required_community_false">No</label>
                          	</div>
                        </fieldset>
                                                
                   	</fieldset>
                   	
                   	<fieldset class="grid">
                   			
                   		<h2>Call-to-Action Settings</h2>
                   		
                   		<fieldset>
	                        <label>Agents</label>
                        	<?php if (!empty($agents) && is_array($agents)) { ?>
                        		<div class="scrollable-agents">
	                        		<?php foreach ($agents as $agent) { ?>
	                        			<label><input type="checkbox" name="cta_agents[]" value="<?=$agent['id'];?>" <?=(!empty($settings['cta_agents']) && is_array($settings['cta_agents']) && in_array($agent['id'], $settings['cta_agents']) ? 'checked="checked"' : '');?>> <?=$agent['first_name'];?> <?=$agent['last_name'];?></label>
	                        		<?php } ?>	
	                        	</div>
                        	<?php } ?>
	                        <p class="tip">Selected agents will display on the community and listing details CTAs.</p>
                  		</fieldset>
                  		
                  		<?php if (!empty(Settings::getInstance()->MODULES['REW_LENDERS_MODULE'])) { ?>
	                  		<fieldset>
		                        <label>Lenders</label>
	                        	<?php if (!empty($lenders) && is_array($lenders)) { ?>
	                        		<div class="scrollable-agents">
		                        		<?php foreach ($lenders as $lender) { ?>
		                        			<label><input type="checkbox" name="cta_lenders[]" value="<?=$lender['id'];?>" <?=(!empty($settings['cta_lenders']) && is_array($settings['cta_lenders']) && in_array($lender['id'], $settings['cta_lenders']) ? 'checked="checked"' : '');?>> <?=$lender['first_name'];?> <?=$lender['last_name'];?></label>
		                        		<?php } ?>	
		                        	</div>
	                        	<?php } ?>
		                        <p class="tip">Selected lenders will display on the community and listing details CTAs.</p>
	                  		</fieldset>
	                 	<?php } ?>
                  			
                  	</fieldset>
                  	
            	</div>
            	
		    </section>
	
		</section>
		
		<section class="col w12 p1">
        	<button type="submit" class="btn btn--positive">Save</button>
       	</section>

	</form>

<?php } ?>
