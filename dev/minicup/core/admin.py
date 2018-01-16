# coding=utf-8
from django.contrib import admin
from django.contrib import messages
from django.db.models import QuerySet
from django.forms.models import ModelForm
from django.utils.translation import ugettext as _

from .models import TeamInfo, MatchTerm, Match


def swap(model_admin: admin.ModelAdmin, request, queryset: QuerySet):
    if queryset.count() != 2:
        model_admin.message_user(request, _('Swapped can be only directly two matches.'), level=messages.ERROR)
        return

    first, second = queryset  # type: Match, Match
    first.match_term, second.match_term = second.match_term, first.match_term
    first.save(update_fields=['match_term', ])
    second.save(update_fields=['match_term', ])
    model_admin.message_user(request, _('Successfully swapped.'), level=messages.SUCCESS)


swap.short_description = _('Swap terms of two selected matches.')


@admin.register(Match)
class MatchAdmin(admin.ModelAdmin):
    date_hierarchy = 'match_term__day__day'
    list_filter = (
        'match_term__day__year__slug',
        'category__name',
    )

    list_display = (
        '__str__',
        'category',
        'match_term'
    )

    search_fields = (
        'match_term__start',
        'category__name',
        'home_team_info__name',
        'away_team_info__name',
    )

    actions = [swap, ]

    class MatchForm(ModelForm):
        def __init__(self, *args, **kwargs):
            super().__init__(*args, **kwargs)
            if self.instance and self.instance.category:
                self.fields['match_term'].queryset = MatchTerm.objects.filter(
                    day__year=self.instance.category.year
                )

        class Meta:
            model = Match
            fields = '__all__'

    form = MatchForm


@admin.register(TeamInfo)
class TeamInfoAdmin(admin.ModelAdmin):
    list_filter = (
        'category__year',
        'category__name',
    )
    list_display = (
        '__str__',
        'tag',
    )


@admin.register(MatchTerm)
class MatchTermAdmin(admin.ModelAdmin):
    date_hierarchy = 'day__day'
