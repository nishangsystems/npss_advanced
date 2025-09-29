<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>{{config('app.name')}}</title>

		<meta name="description" content="User login page" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}" />
		<link rel="stylesheet" href="{{asset('assets/font-awesome/4.5.0/css/font-awesome.min.css')}}" />
		<link rel="stylesheet" href="{{asset('assets/css/fonts.googleapis.com.css')}}" />
		<link rel="stylesheet" href="{{asset('assets/css/ace.min.css')}}" />
		<link rel="stylesheet" href="{{asset('assets/css/custom.css')}}" />
		<!-- <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

        <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-show-password/1.0.3/bootstrap-show-password.min.js"></script> -->

		@php
			$bg1 = \App\Http\Controllers\HomeController::getColor('background_color_1');
			$bg2 = \App\Http\Controllers\HomeController::getColor('background_color_2');
			$bg3 = \App\Http\Controllers\HomeController::getColor('background_color_3');
			$bg_path = \App\Helpers\Helpers::instance()->getBackground();
		@endphp

		<style>
			body{
				background-image: url("{{asset('assets/images/background1.png')}}");
				background-position: center;
				background-size: cover;
				background-repeat: no-repeat;
				background-attachment: fixed;


			}

				/* Rectangle 23 */
			#login-frame{
				position: relative;
				width: 350px;
				height: 480px;
				min-height: fit-content;
				margin-inline: auto;
				border-radius: 24px;
			}
				/* Rectangle 23 */
			#login-frame .rect1{
				position: absolute;
				width: 100%;
				height: 100%;
				min-height: fit-content;
				background: #DBA622;
				border-radius: 24px;
				top: -9px;
			}

				/* Rectangle 22 */
			#login-frame .rect2{
				position: absolute;
				width: 100%;
				height: 100%;
				min-height: fit-content;
				margin-inline: auto;
				background: #670404;
				box-shadow: 0px 0px 34px rgba(0, 0, 0, 0.25);
				border-radius: 24px;
				left: 9px;
			}
				/* input bg */
			#login-frame .main-rect{
				position: absolute;
				width: 100%;
				height: 100%;
				min-height: fit-content !important;
				background-color: white;
				border-radius: 24px;
			}

			#login-frame .main-rect div{
				background-color: white;
			}




		    a{
		        text-decoration:none;
		        font-weight:bold;
		        font-size:16px;
		        color:#fff;
		    }

		</style>
	</head>

	<body class="login-layout" id="frame">
		<div class="main-container px-5" style="padding-inline: 2rem;">
			<div class="py-5 mx-5 w-100 text-uppercase text-center" style="padding: 2rem; font-weight: bolder; vertical-align: middle;">
				<h3> <span style="color: white; text-shadow: -1px -1px 0 #670f0e, 1px -1px 0 #670f0e, -1px 1px 0 #670f0e, 1px 1px 0 #670f0e; font-weight: bolder; font-size: xx-large;">VamVam</span> <br> <b>{{__('text.word_for')}} <br>
					<span style="color: white; text-shadow: -1px -1px 0 #670f0e, 1px -1px 0 #670f0e, -1px 1px 0 #670f0e, 1px 1px 0 #670f0e; font-weight: bolder; font-size: xx-large; transform: skew(12deg, 17deg) !important;">{{env('APP_NAME')}}</span></b>
				</h3>
			</div>
			<div class="main-content">
				<div class="w-100">
						<div class="login-container" id="login-frame">

							<div class="rect1"></div>

				  			<div class="rect2"></div>
							<div class="position-relative main-rect " >
								<div id="login-box" class="login-box visible widget-box no-border">
									<div class="widget-body">
										<div  class=" clearfix"  style="border: 0px; font-size: xsmall !important; width: 77% !important; margin-inline: auto; font-weight: bolder !important;">
											<button class="btn btn-sm border-0 mx-3" style=" color: white; background-color: black !important;" onclick="toggle_others()">{{__('text.student_slash_staff_login')}}</button> |
											<button class="btn btn-sm border-0 mx-3" style=" color: white; background-color: black !important;" onclick="toggle_parents()">{{__('text.parent_login')}}</button>
										</div>
										<div class="widget-main">
											<span style="font-size: small; margin-bottom: 1rem;">{{__('auth.auth_request')}}</span>

											@if(Session::has('error'))
												<div class="alert alert-danger"><em> {!! session('error') !!}</em>
												</div>
											@endif


											@if(Session::has('e'))
												<div class="alert alert-danger"><em> {!! session('e') !!}</em>
												</div>
											@endif

											@if(Session::has('success'))
												<div class="alert alert-success"><em> {!! session('success') !!}</em>
												</div>
											@endif

											@if(Session::has('s'))
												<div class="alert alert-success"><em> {!! session('s') !!}</em>
												</div>
											@endif
											@if(Session::has('message'))
												<div class="alert alert-success"><em> {!! session('message') !!}</em>
												</div>
											@endif

											@if(Session::has('m'))
												<div class="alert alert-success"><em> {!! session('m') !!}</em>
												</div>
											@endif
											<div class="space-6"></div>

											<form method="post" action="{{route('login.submit')}}">
											@csrf
												<fieldset style="color: black;">
													<div id="for_others" class="">
														<label class="block clearfix">
															<span class="text-capitalize">{{__('text.word_username')}}</span>
															<span class="block input-icon input-icon-right" style="background-color: white !important;">
																<input type="text" required class="form-control" value="{{old("username")}}" name="username" style="border-radius: 0.5rem !important; background-color: white !important; color: black" />
															</span>
															@error('username')
																<span class="invalid-feedback red" role="alert">
																	<strong>{{ $message }}</strong>
																</span>
															@enderror
														</label>
													</div>
													<div id="for_parents" class="hidden">
														{{-- @if(!(isset($phone) and $phone != null))
															<label class="block clearfix">
																<span class="text-capitalize">{{__('text.word_country')}}</span>
																<span class="block input-icon input-icon-right">
																	<select required id="country_picker" class="form-control" style="border-radius: 0.5rem !important; background-color: white !important; color: black" onchange="code_change(event)">
																		<option></option>
																		@foreach (config('country-phone-codes') as $code)
																			<option value="+{{ $code['code'] }}">{{ $code['country'].' (+'.$code['code'].')' }}</option>
																		@endforeach
																	</select>
																</span>
															</label>
														@endif

														<label class="block clearfix">
															<span class="text-capitalize">{{__('text.parents_phone_number')}}</span>
															<span class="block input-icon input-icon-right">
																<input type="text" required name="username" id="parent_phone" class="form-control" value="{{ $phone??'' }}"  style="border-radius: 0.5rem !important; background-color: white !important; color: black"/>
															</span>
														</label> --}}
													</div>
													<div class="space"></div>
													<label class="block clearfix">
														<span class="text-capitalize">{{__('text.word_password')}}</span>
														<span class="block input-icon input-icon-right">
															<input  type="password" id="password" name="password" data-toggle="password" required class="form-control" style="border-radius: 0.5rem !important; background-color: white !important; color: black"/>
														</span>
														@error('password')
															<span class="invalid-feedback red" role="alert">
																<strong>{{ $message }}</strong>
															</span>
														@enderror
													</label>

													<div class="space"></div>

													<div class="clearfix">
														<button type="submit" class="form-control btn-black btn-sm" style="border-radius: 2rem; background-color: black; border: 1px solid black; color: white;">
															{{-- <i class="ace-icon fa fa-key"></i> --}}
															<span class="bigger-110">{{__('text.log_in')}}</span>
														</button>
													</div>

													<div class="space-4"></div>
												</fieldset>
											</form>
										</div><!-- /.widget-main -->

										<div class="clearfix toolbar"  style=" border: 0px;  font-size: xsmall !important; width: 77% !important; margin-inline: auto; ">
											{{-- <div>
												<a  href="#" data-target="#forgot-username-box" class="forgot-username-link" style="color: black !important; text-decoration: underline !important;">
													{{__('text.forgot_username')}}
												</a>
											</div> --}}
											
												<a  href="#" data-target="#forgot-box" class="text-center form-control btn-black btn-sm" style="border-radius: 2rem; background-color: black; border: 1px solid black; color: white; font-weight: normal !important;">
													<span class="bigger-110">{{__('text._forgot_password')}}</span>
												</a>
											<div>
											</div>

										</div>
										<div class=" clearfix"  style="border: 0px; font-size: xsmall !important; width: 77% !important; margin-inline: auto; ">
											<a href="{{ route('create_parent') }}" class="btn btn-sm border-0 mx-auto" style="border-radius: 0.3rem; pasdding: 0.2rem 0.6rem; width:47%; margin-inline: 3px; border: 0 !important; background-color: #DBA622 !important; color: white !important; font-weight: normal !important;">{{__('text.parent_account')}}</a>
											<a href="{{ route('registration') }}" class="btn btn-sm border-0 mx-auto" style="border-radius: 0.3rem; pasdding: 0.2rem 0.6rem; width:47%; margin-inline: 3px; border: 0 !important; background-color: #DBA622 !important; color: white !important; font-weight: normal !important;">{{__('text.student_account')}}</a>
										</div>
									</div><!-- /.widget-body -->
								</div><!-- /.login-box -->

								<div id="forgot-box" class="forgot-box widget-box no-border">
									<div class="widget-body">
										<div class="widget-main">
											<h4 class="bigger text-capitalize" style="color: black; font-size: xlarge;">
											 	<b>{{__('text.forgot_password')}}</b>
											</h4>
											<span style="font-size: small; margin-bottom: 1rem;">{{__('text.pass_reset_phrase')}}</span>

											<form method="POST" action="{{ route('reset_password_without_token') }}" style="padding-block: 1rem !important;">
											@csrf
												<fieldset>
													<label class="block clearfix">
														<span class="text-capitalize">{{__('text.word_email')}}</span>
														<span class="block input-icon input-icon-right">
															<input type="email" required name="email" class="form-control"  style="border-radius: 0.5rem !important; background-color: white !important; color: black"/>
														</span>
													</label>

													{{-- <label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="checkbox" name="type">  Am a student
														</span>
													</label> --}}

													<div class="clearfix">
														<button type="submit" class="form-control btn-black btn-sm"  style="border-radius: 2rem; background-color: black; border: 1px solid black; color: white; text-transform: capitalize; margin-block: 2rem;">
															{{__('text.reset_password')}}
														</button>
													</div>
												</fieldset>
											</form>
										</div><!-- /.widget-main -->

										<div class="toolbar clearfix" style="border: 0px; padding-inline: 4rem;">
											<a href="#" data-target="#login-box" class="form-control btn-black btn-sm text-center"  style="border-radius: 2rem; background-color: black; border: 1px solid black; color: white; text-transform: capitalize; font-weight: normal !important;">
												<i class="ace-icon fa fa-arrow-left"></i>
												{{__('text.back_to_login')}}
											</a>
										</div>
									</div><!-- /.widget-body -->
								</div><!-- /.forgot-box -->

								<div id="forgot-username-box" class="forgot-box widget-box no-border">
									<div class="widget-body">
										<div class="widget-main">
											<h4 class="bigger text-capitalize" style="color: black; font-size: xlarge;">
											 	<b>{{__('text.recover_your_username')}}</b>
											</h4>
											<span style="font-size: small; margin-bottom: 1rem;">{{__('text.recover_username_phrase')}}</span>

											<form method="POST" action="{{ route('recover_username') }}" style="padding-block: 3rem !important;">
												@csrf
												<fieldset>
													<label class="block clearfix">
														<span class="text-capitalize">{{__('text.word_matricule')}}</span>
														<span class="block input-icon input-icon-right">
															<input type="text" required name="matric" class="form-control"  style="border-radius: 0.5rem !important; background-color: white !important; color: black"/>
														</span>
													</label>

													<label class="block clearfix">
														<span class="block input-icon input-icon-right" style="text-transform: capitalize !important;">
															<input type="checkbox" name="remember">  {{__('text.remember_maticule')}}
														</span>
													</label>

													<div class="clearfix">
														<button type="submit" class="form-control btn-black btn-sm"  style="border-radius: 2rem; background-color: black; border: 1px solid black; color: white; text-transform: capitalize; margin-block: 2rem;">
															{{__('text.recover_username')}}
														</button>
													</div>
												</fieldset>
											</form>
										</div><!-- /.widget-main -->

										<div class="toolbar" style="border: 0px;">
											<a href="#" data-target="#login-box" class=" text-danger" style="border: 0px; font-size: xsmall !important; width: 77% !important; margin-inline: auto; text-decoration: underline !important">
												<i class="ace-icon fa fa-arrow-left"></i>
												{{__('text.back_to_login')}}
											</a>
										</div>
									</div><!-- /.widget-body -->
								</div><!-- /.forgot-box -->


							</div>
						</div>
				</div><!-- /.row -->
			</div><!-- /.main-content -->

			<div style="display: flex; justify-content: center; padding-block: 3rem; text-align: center; text-transform: capitalize; color: black !important;">
				<span>{{__('text.powered_by')}} <b> {{__('text.nishang_system')}} </b></span>
			</div>
		</div><!-- /.main-container -->


{{--		@include('inc.student.footer')--}}
				</div>
		<script src="{{asset('assets/js/jquery-2.1.4.min.js')}}"></script>
		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='{{asset('assets/js/jquery.mobile.custom.min.js')}}'>"+"<"+"/script>");
		</script>

		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			jQuery(function($) {
			 $(document).on('click', '.toolbar a[data-target]', function(e) {
				e.preventDefault();
				var target = $(this).data('target');
				$('.widget-box.visible').removeClass('visible');//hide others
				$(target).addClass('visible');//show target
			 });
			});
		</script>
		<script type="text/javascript">

		let toggle_parents = function(){
			let html = `@if(!(isset($phone) and $phone != null))
							<label class="block clearfix">
								<span class="text-capitalize">{{__('text.word_country')}}</span>
								<span class="block input-icon input-icon-right">
									<select required id="country_picker" class="form-control" style="border-radius: 0.5rem !important; background-color: white !important; color: black" onchange="code_change(event)">
										@foreach (config('country-phone-codes') as $code)
											<option value="+{{ $code['code'] }} {{ ($code['autoselect']??null) == 1 ? 'selected' : '' }}">{{ $code['country'].' (+'.$code['code'].')' }}</option>
										@endforeach
									</select>
								</span>
							</label>
						@endif

						<label class="block clearfix">
							<span class="text-capitalize">{{__('text.parents_phone_number')}}</span>
							<span class="block input-icon input-icon-right">
								<input type="text" required name="username" id="parent_phone" class="form-control" value="+237{{ $phone??'' }}" style="border-radius: 0.5rem !important; background-color: white !important; color: black"/>
							</span>
						</label>`;
			$('#for_others').html(null);
			$('#for_others').addClass('hidden');
			$('#for_parents').removeClass('hidden');
			$('#for_parents').html(html);
		}

		let toggle_others = function(){
			let html = `<label class="block clearfix">
					<span class="text-capitalize">{{__('text.word_username')}}</span>
					<span class="block input-icon input-icon-right" style="background-color: white !important;">
						<input type="text" required class="form-control" value="{{old("username")}}" name="username" style="border-radius: 0.5rem !important; background-color: white !important; color: black" />
					</span>
					@error('username')
						<span class="invalid-feedback red" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</label>`;
			$('#for_parents').html(null);
			$('#for_parents').addClass('hidden');
			$('#for_others').removeClass('hidden');
			$('#for_others').html(html);
		}

		let p_phone = '';
		$(document).ready(function(){
			p_phone = $('#parent_phone').val();
		})
		let code_change = function(event){
			let val = event.target.value;
			$('#parent_phone').val(val);
		}
$("#password").password('toggle');

</script>

	</body>
</html>
