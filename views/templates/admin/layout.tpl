{**
*  @author    MaTe0r <mateo.rinaldi.pro@gmail.com>
*  @copyright MaTe0r
*  @license   MaTe0r All right reserved
*}

<!-- check if update is available for module -->
{if $mr_security_version_last !== $mr_security_version}
<div class="alert alert-warning">
    {l s='A new module update is available.' mod='mr_security'}
    <a href="#" data-toggle="modal" data-target="#modal_upgrade">
        {l s='Download' mod='mr_security'} <b>{$mr_security_name} v{$mr_security_version_last}</b>
    </a>
</div>
{/if}

<div id="mr_security_admin">
    <div class="alert alert-info">
        {l s='Vulnerabilities are recovered from' mod='mr_security'}
        <a href="https://security.friendsofpresta.org" target="_blank">
            <i class="fa fa-external-link-alt"></i> https://security.friendsofpresta.org
        </a>
    </div>

    <nav class="navbar navbar-default panel p-0">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="collapsed navbar-toggle" data-toggle="collapse" data-target="#mr_security_navbar">
                    <div class="d-flex align-items-center">
                        <div>{l s='Menu' mod='mr_security'}&nbsp;&nbsp;</div>
                        <div>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </div>
                    </div>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="mr_security_navbar">
                <ul class="nav navbar-nav d-flex w-100">

                    <li {if $mr_security_controller|default:"" === "MrSecurityConfigAdmin"}class="active"{/if}>
                        <a href="{$mr_security_link_config}"><i class="fa fa-exclamation-circle"></i> {l s='Vulnerabilities' mod='mr_security'}</a>
                    </li>

                    <li>
                        <a href="{$mr_security_support_link}" target="_blank"><i class="icon-support"></i> {l s='Support' mod='mr_security'}</a>
                    </li>
                    
                    <li style="background-color: #ADDFFF; margin-left: auto">
                        <a style="font-weight: bold;font-size: 0.8rem;color: #333;">{$mr_security_name}</a>
                    </li>

                    <li style="background-color: pink">
                        <a style="font-weight: bold;font-size: 0.8rem;color: #333;">v{$mr_security_version}</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    {if isset($mr_security_error) && $mr_security_error}
    <div class="alert alert-danger">{$mr_security_error} <button data-dismiss="alert" type="button" class="close">×</button></div>
    {/if}

    {if isset($mr_security_success) && $mr_security_success}
    <div class="alert alert-success">{$mr_security_success} <button data-dismiss="alert" type="button" class="close">×</button></div>
    {/if}

    <div class="mr_security_content">
    {block name="content"}{/block}
    </div>


    <div id="modal_upgrade" class="modal fade" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">

				<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h5 class="modal-title">{l s='Module update' mod='mr_security'}</h5>
				</div>

				<div class="modal-body">

                    <p id="step_download">
                        <input type="hidden" name="url" value="{$mr_security_link_config}&action=download">
                        <i class="fa fa-spin fa-spinner fa-2x"></i> {l s='Download module' mod='mr_security'}
                    </p>

                    <p id="step_install">
                        <input type="hidden" name="url" value="{$mr_security_link_config}&action=install">
                        <i class="fa fa-2x fa-spin fa-spinner"></i> {l s='Module installation' mod='mr_security'}
                    </p>
					
				</div>

			</div>
		</div>
    </div>
</div>