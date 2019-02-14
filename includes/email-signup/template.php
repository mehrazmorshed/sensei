<div id="mc_embed_signup">
    <form action="https://senseilms.us19.list-manage.com/subscribe/post?u=7a061a9141b0911d6d9bafe3a&amp;id=278a16a5ed" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
        <input type="hidden" name="SOURCE" value="PLUGIN">
        <div id="mc_embed_signup_scroll">
			<h2>Join Our Mailing List!</h2>
			<p>
				We'd love to update you occasionally about the latest developments around Sensei, the latest features and conversations around the upcoming roadmap. You can opt out of these any time.
			</p>
			<div id="mergeRow-gdpr" class="mergeRow gdpr-mergeRow content__gdprBlock mc-field-group">
				<div class="content__gdpr">
					<label class="checkbox subfield" for="gdpr_34447">
						<input type="checkbox" id="gdpr_34447" name="gdpr[34447]" value="Y" class="av-checkbox ">
						<span>Yes, please send me occasional emails about Sensei</span>
					</label>
				</div>
			</div>
			<div class="email-input">
				<div class="mc-field-group">
					<input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL" placeholder="Enter your email address">
				</div>
				<div class="content__gdprLegal">
					<p>We use Mailchimp as our marketing platform. By clicking below to subscribe, you acknowledge that your information will be transferred to Mailchimp for processing. <a href="https://mailchimp.com/legal/" target="_blank">Learn more about Mailchimp's privacy practices here.</a></p>
				</div>
			</div>
			<div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_7a061a9141b0911d6d9bafe3a_278a16a5ed" tabindex="-1" value=""></div>
			<div class="buttons clear">
				<a href="#close" id="mc-embedded-cancel" class="button" rel="modal:close">Not Now</a>
				<input type="submit" value="Yes, please!" name="subscribe" id="mc-embedded-subscribe" class="button-primary">
			</div>
        </div>
    </form>
</div>

<script type="text/javascript">
	jQuery( document ).ready( function( $ ) {
		setTimeout( function() {
			$( '#mc_embed_signup' ).modal( {
				fadeDuration: 250,
				showClose: false,
			} );
		}, 1000 );

		$( 'body' ).on( 'submit', '#mc_embed_signup', function( event ) {
			setTimeout( function() {
				$.modal.close();
			} );
		} );

		$( 'body' ).on( 'change', '#mc_embed_signup #gdpr_34447', function( event ) {
			if ( $( event.target ).is( ':checked' ) ) {
				$( '#mc_embed_signup .email-input' ).show();
			} else {
				$( '#mc_embed_signup .email-input' ).hide();
			}
		} );
	} );
</script>

<style>
	#mc_embed_signup {
		border-radius: 0;
	}
	#mc_embed_signup h2 {
		text-transform: uppercase;
	}
	#mc_embed_signup input[type=email] {
		width: 100%;
		height: 2.5em;
		border-radius: 3px;
		background-color: #eee;
	}
	#mc_embed_signup input::placeholder {
		text-transform: uppercase;
		/* font-weight: bold; */
	}
	#mc_embed_signup .email-input {
		display: none;
		margin-top: 2em;
	}
	#mc_embed_signup .email-input {
		display: none;
		margin-top: 2em;
	}
	#mc_embed_signup .buttons {
		margin-top: 2em;
		margin-bottom: 2em;
		text-align: right;
	}
	#mc_embed_signup .content__gdprLegal {
		font-style: italic;
	}
</style>
