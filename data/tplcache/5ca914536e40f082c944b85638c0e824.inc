<li class="{dede:field.currentstyle/}"><a id="{dede:field name='id'/}" href="{dede:field name='typeurl'/}"> <i class="icon-fixed-width icon-00{dede:global.itemindex/}"></i>{dede:field name='typename'/}</a>
          <ul class="sub-menu">
            {dede:channel type='son' noself='yes'}
            <li><a href="[field:typelink/]">[field:typename/]</a></li>
            {/dede:channel}
          </ul>
        </li>