import {SettingsComponent} from './settings.component';
import {Routes} from '@angular/router';
import {PartnerResolveService} from '../../shared/resolve-services/partner-resolve.service';
import {AuthGuardService} from '../../shared/guard-services/auth-guard.service';
import {FlatrateGuardService} from '../../shared/guard-services/flatrate-guard.service';
import {BreadcrumbResolveService} from '../../shared/resolve-services/breadcrumb-resolve.service';

const routes: Routes = [
  {
    path: '',
    component: SettingsComponent,
    children: [
      {
        path: 'lexoffice',
        loadChildren: () => import('./pages/lexoffice-settings/lexoffice-settings.module').then(m => m.LexofficeSettingsModule),
        data: {
          breadcrumb: 'lexoffice_settings',
        },
        resolve: {
          breadcrumbs: BreadcrumbResolveService,
          partnerdb: PartnerResolveService,
        },
        canActivate: [AuthGuardService, FlatrateGuardService]
      }
    ],
  }
]