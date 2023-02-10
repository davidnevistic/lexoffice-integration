import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LexofficeSettingsComponent } from './lexoffice-settings.component';
import { FormsModule } from '@angular/forms';
import { Routes, RouterModule } from '@angular/router';
import { TranslateModule } from '@ngx-translate/core';
import { CalioButtonModule } from 'src/app/calio-lib/calio-button/calio-button.module';
import { CalioCardModule } from 'src/app/calio-lib/calio-card/calio-card.module';
import { CalioPipesModule } from 'src/app/calio-lib/calio-pipes/calio-pipes.module';
import { MatSlideToggleModule } from '@angular/material/slide-toggle';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';

const routes: Routes = [{
  path: '',
  component: LexofficeSettingsComponent
}];

@NgModule({
  declarations: [
    LexofficeSettingsComponent
  ],
  imports: [
    CommonModule,
    RouterModule.forChild(routes),
    CalioPipesModule,
    TranslateModule,
    CalioCardModule,
    CalioButtonModule,
    FormsModule,
    MatSlideToggleModule,
    MatCheckboxModule,
    MatProgressSpinnerModule
  ]
})
export class LexofficeSettingsModule { }
