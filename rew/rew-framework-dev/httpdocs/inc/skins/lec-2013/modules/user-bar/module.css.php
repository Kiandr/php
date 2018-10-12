.idx-user-bar
{
	width: 100%; float: left; clear: both; /* clear */
	background: #E0D9D2;
	border: 1px solid #E0D9D2;
	margin: 20px 0 40px 0;
	padding: 1px 0 0 0;
}

.idx-user-bar p
{
	margin: 0; padding: 5px 20px; font-family: "Proxima N W15 Reg";
	float: left;
}

.idx-user-bar .nav
{
	margin: 0;
	float: right;
}

.idx-user-bar ul,
.idx-user-bar li,
.idx-user-bar a
{
	margin: 0;
	float: left;
}

.idx-user-bar .nav h4
{
	margin: 0;
	float: left;
	display: none;
}

@media only screen and (max-width:780px)
{
	.idx-user-bar .nav h4 {
		display: block;
	}

	#page #body .idx-user-bar nav.horizontal ul li a, #page #body .idx-user-bar .nav.horizontal ul li a
	{
		font-size: 14px;
	}

	.idx-user-bar .nav .navi-refine
	{
		display: inline-block !important;
	}

}