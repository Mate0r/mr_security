{*
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="./layout.tpl"}

{block name="content"}
<div class="panel p-0">

	<h3 style="margin: 0; padding: 0.5rem">
		<i class="fa fa-exclamation-circle"></i>&nbsp;{l s='Vulnerabilities' mod='mr_security'}
	</h3>

	<div class="tab-content" id="pill-tabContent" style="padding: 1rem;">

		{if isset($vulnerabilities) && $vulnerabilities}
		<table class="table table-bordered">

			<thead>
				<tr>
					<th>CVE</th>
					<th>{l s='Category' mod='mr_security'}</th>
					<th>{l s='Product' mod='mr_security'}</th>
					<th>{l s='Status' mod='mr_security'}</th>
					<th>{l s='Action(s)' mod='mr_security'}</th>
				</tr>
			</thead>

			<tbody>
				{foreach $vulnerabilities as $vulnerability}
				<tr data-cve-id="{$vulnerability->cve_id}">
					<td>
						<span data-toggle="tooltip" data-placement="top" title="{$vulnerability->title}">
							<i class="fa fa-question-circle"></i>
						</span>&nbsp;
						<a href="{$vulnerability->getCVELink()}" target="_blank">
							<i class="fa fa-external-link-alt"></i> {$vulnerability->cve_id}
						</a>
					</td>
					<td>
						{if $vulnerability->category === "core"}
							<span class="badge badge-info">{$vulnerability->category}</span>
						{elseif in_array($vulnerability->category, ["module", "modules"])}
							<span class="badge badge-warning">{$vulnerability->category}</span>
						{else}
							<span class="badge badge-secondary">{$vulnerability->category}</span>
						{/if}
					</td>
					<td>{$vulnerability->product}</td>
					<td class="status">
						{if $vulnerability->getStatus() === MrSecurityVulnerability::STATUS_NOT_IMPACTED}
						<span class="badge badge-secondary">{l s='Not impacted' mod='mr_security'}</span>
						{elseif $vulnerability->getStatus() === MrSecurityVulnerability::STATUS_VULNERABLE}
						<span class="badge badge-danger">{l s='Vulnerable' mod='mr_security'}</span>
						{elseif $vulnerability->getStatus() === MrSecurityVulnerability::STATUS_NOT_VULNERABLE}
						<span class="badge badge-success">{l s='Not vulnerable' mod='mr_security'}</span>
						{else}
						<span class="badge badge-info">?</span>
						{/if}
					</td>
					<td>
						<div class="btn-group">
							{if $vulnerability->getStatus() === MrSecurityVulnerability::STATUS_VULNERABLE}
							<a href="{$vulnerability->getLink('patch')}" class="btn btn-sm btn-info btn-ajax disabled" disabled>
								<i class="fa fa-briefcase-medical"></i> {l s='Patch' mod='mr_security'}
							</a>
							{/if}
						</div>
					</td>
				</tr>
				{/foreach}
			</tbody>

		</table>
		{else}
		<div class="alert alert-info" style="margin-bottom: 0">
			{l s='No vulnerabilities to display' mod='mr_security'}
		</div>
		{/if}

	</div>
</div>
{/block}